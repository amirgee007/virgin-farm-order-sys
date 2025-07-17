<?php

namespace Vanguard\Http\Controllers\Web;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\OrderConfirmationMail;
use Vanguard\Models\Carrier;
use Vanguard\Models\Cart;
use Vanguard\Models\Order;
use Vanguard\Models\Product;
use Vanguard\Models\PromoCode;
use Vanguard\Models\ShippingAddress;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class OrdersController extends Controller
{

    public function index()
    {

        $search = \request()->search;
        $user = \request()->user_id;
        $sales_rep = \request()->sales_rep;

        $yesId = str_starts_with($search, 'WO');

        $user_id = myRoleName() == 'Admin' ? null : auth()->id();
        #for clients show only client but for admin show ALL.

        $query = Order::with('items')->latest();

        $users = [$user_id => auth()->user()->first_name];

        #client showing and else is in ADMIN.
        if ($user_id)
            $query->where('user_id', $user_id);
        else {
            $users = User::where('status', UserStatus::ACTIVE)
                ->orderby('first_name')
                ->pluck('first_name', 'id')
                ->toArray();

            $users = [0 => 'Show All'] + $users;
        }

        if ($user) {
            $query->where('user_id', $user);
        }

        if ($sales_rep) {
            $query->where('sales_rep', $sales_rep);
        }

        if ($yesId) {
            $id = str_replace("WO", "", $search);;
            $query->where(function ($q) use ($id) {
                $q->orWhere('id', 'like', $id);
            });
        } elseif ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%");
                $q->orWhere('company', 'like', "%{$search}%");
                $q->orWhere('phone', 'like', "%{$search}%");
                $q->orWhere('shipping_address', 'like', "%{$search}%");
            });
        }

        $count = (clone $query)->count();
        $orders = $query->paginate(15);

        if ($sales_rep || $search) {
            $orders->appends([
                'sales_rep' => $sales_rep,
                'search' => $search
            ]);
        }

        $isAdmin = myRoleName() == 'Admin';
        $salesRep = getSalesReps();
        return view('orders.index', compact('orders', 'count', 'user_id', 'users', 'isAdmin', 'salesRep'));
    }

    public function updateOrder($id, $type)
    {
        #markCompeted, ##delete
        $order = Order::find($id);

        if ($type == 'markCompeted') {
            $order->update([
                'is_active' => 0
            ]);

            #admin notify about status.
            $message = 'Your order status has been updated : Wo-' . $order->id;
            addOwnNotification($message, $order->id, $order->user_id);
        } else if ($type == 'markNotApproved') {
            $order->update([
                'is_active' => 2
            ]);

            #admin notify about status. and We are reviewing what to do in this case–as the product will be returned to the inventory pending
            $message = 'Your order status has been updated : Wo-' . $order->id . ' and our sales representative will contact you soon.';
            addOwnNotification($message, $order->id, $order->user_id);
        } else if ($type == 'delete') {
            $order->items()->delete();
            $order->delete();
        }

        session()->flash('app_message', 'The order has been updated successfully.');
        return back();

    }

    public function sendEmailCopy(Request $request)
    {
        try {
            $order = Order::find($request->orderId);
            $user = User::where('id', $order->user_id)->first();

            $emails = explode(',', $request->input('emails'));

            $emails = array_filter($emails, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });

            \Mail::to($emails)->send(new OrderConfirmationMail($order, $user));

            return response()->json(['message' => 'Emails sent successfully regarding Order ID ' . $order->user_id]);
        } catch (\Exception $ex) {
            Log::error('sendEmailCopy ' . $order->user_id . ' order id: ' . $request->input('emails'));
        }
    }

    public function addOnOrderUpdate(Request $request)
    {
        try {

            User::where('id', auth()->id())->update(['edit_order_id' => $request->order_id]);
            auth()->user()->fresh();

            $user = auth()->user();

            $order = Order::find($request->order_id);
            $data['date'] = $request->order_id > 1 ? $order->date_shipped : $user->last_ship_date;
            $data['success'] = true;
            return response()->json($data);

        } catch (\Exception $exc) {
            Log::error('addOnOrderUpdate error plz check why .' . $exc->getMessage());

            $data['success'] = false;
            return response()->json($data);
        }
    }

    public function dateCarrierValidation(Request $request)
    {
        $user = auth()->user();
        $dateShipped = $request->input('date_shipped', null); #$user->last_ship_date
        $usersCarrierId = $request->input('carrier_id', $user->carrier_id_default);
        $lastShipDate = $user->last_ship_date;

        $cartExist = Cart::mineCart()->exists();

        // Initialize response data
        $response = [
            'error' => $cartExist,
            'cartExist' => $cartExist,
        ];

        $dayOfWeek = Carbon::parse($dateShipped)->dayOfWeekIso; // 1 = Monday, 7 = Sunday
        $today = now()->toDateString();
        $currentTime = now();
        $cutoffTime = Carbon::createFromTimeString('15:30:00'); // 3:30 PM cutoff time

        // Carrier Rule: Virgin Farms (ID 17) – only allow Monday for Tuesday delivery
        if ($usersCarrierId == 17) {
            if ($dayOfWeek != 1) {
                $response['error'] = true;
                $response['old_ship_date'] = $lastShipDate;
                $response['message'] = "Choose Monday as your ship date for Tuesday delivery with Virgin Farms. Alternatively, FedEx is available.";
                return response()->json($response);
            }
        }

        // Carrier Rule: FedEx (Customer Acct = 19, Ecuador = 20, Priority Overnight = 23)
        $fedexCarrierIds = [19, 20, 23];
        if (in_array($usersCarrierId, $fedexCarrierIds)) {
            if ($dayOfWeek == 5) { // 5 = Friday
                $response['error'] = true;
                $response['old_ship_date'] = $lastShipDate;
                $response['message'] = "FedEx does not ship on Fridays. Please select a Monday–Thursday ship date.";
                return response()->json($response);
            }
        }

        // Carrier Rule: Pick Up (32) and FedEx Priority Overnight (23) cutoff for same-day shipping
        $restrictedCarriers = [23, 32];
        if (!$cartExist && $dateShipped === $today && $currentTime->greaterThan($cutoffTime) && in_array($usersCarrierId, $restrictedCarriers)) {
            $response['error'] = true;
            $response['old_ship_date'] = $lastShipDate;
            $response['CutoffPassed'] = "The cutoff time (3:30 PM) has passed for this carrier today. Please select a future ship date.";
        }

        #////////////////////This above logic is used at two places plz keep noted so one change also do second change.

        return response()->json($response);
    }

}

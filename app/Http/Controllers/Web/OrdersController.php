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
        $dateShipped = $request->input('date_shipped', $user->last_ship_date);
        $usersCarrierId = $request->input('carrier_id', $user->carrier_id_default);
        $lastShipDate = $user->last_ship_date;

        $cartExist = Cart::mineCart()->exists();

        // Initialize response data
        $response = [
            'error' => $cartExist,
            'cartExist' => $cartExist,
        ];

        // Get the day of the week for the selected ship date
        $dayOfWeek = Carbon::parse($dateShipped)->dayOfWeekIso; // 1 = Monday, 7 = Sunday

        // 🚫 Disable VF Carrier (ID 17) on Wed, Thu, Fri
        if ($usersCarrierId == 17 && in_array($dayOfWeek, [3, 4, 5])) {
            $response['error'] = true;
            $response['VFNotAllowed'] = 'VF carrier is only available until Tuesday. Please choose FedEx or another carrier for Wednesday, Thursday, or Friday.';
            return response()->json($response);
        }

        // If the date has changed to today and no cart exists, check cutoff conditions
        if (!$cartExist && $dateShipped === now()->toDateString()) {
            $currentTime = now();
            $cutoffTime = Carbon::createFromTimeString('15:30:00'); // 3:30 PM cutoff time
            $restrictedCarriers = [23, 32]; // PU and FedEx

            // If current time is past cutoff and carrier is in the restricted list
            if ($currentTime->greaterThan($cutoffTime) && in_array($usersCarrierId, $restrictedCarriers)) {
                $response['error'] = true;
                $response['old_ship_date'] = $lastShipDate;
            }
        }

        return response()->json($response);
    }
}

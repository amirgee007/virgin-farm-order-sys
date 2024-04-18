<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\OrderConfirmationMail;
use Vanguard\Models\Carrier;
use Vanguard\Models\Order;
use Vanguard\Models\Product;
use Vanguard\Models\ShippingAddress;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class OrdersController extends Controller
{

    public function index(){

        $search = \request()->search;
        $user = \request()->user_id;

        $yesId = str_starts_with($search, 'WO');

        $user_id = myRoleName() == 'Admin' ? null : auth()->id();
        #for clients show only client but for admin show ALL.

        $query = Order::with('items')->latest();

        $users = [$user_id => auth()->user()->first_name];

        #client showing and else is in ADMIN.
        if($user_id)
            $query->where('user_id' , $user_id);
        else{
            $users = User::where('status' , UserStatus::ACTIVE)
                ->orderby('first_name')
                ->pluck('first_name', 'id')
                ->toArray();

            $users = [0 => 'Show All']+$users;
        }

        if($user){
            $query->where('user_id' , $user);
        }

        if($yesId){
            $id = str_replace("WO","",$search);;
            $query->where(function ($q) use ($id) {
                $q->orWhere('id', 'like', $id);
            });
        }

        elseif($search){
            $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%");
                $q->orWhere('company', 'like', "%{$search}%");
                $q->orWhere('phone', 'like', "%{$search}%");
                $q->orWhere('shipping_address', 'like', "%{$search}%");
            });
        }

        $count = (clone $query)->count();
        $orders = $query->paginate(100);

        $isAdmin = myRoleName() == 'Admin';
        return view('orders.index' , compact('orders','count' , 'user_id' , 'users' , 'isAdmin'));
    }

    public function updateOrder($id, $type){
        #markCompeted, ##delete
        $order = Order::find($id);

        if($type == 'markCompeted'){
            $order->update([
                'is_active' => 0
            ]);
        }
        if($type == 'delete'){
            $order->items()->delete();
            $order->delete();
        }

        session()->flash('app_message', 'The order has been updated successfully.');
        return back();

    }

    public function sendEmailCopy(Request $request){

        $order = Order::find($request->orderId);
        $user = User::where('id' , $order->user_id)->first();

        $emails = explode(',', $request->input('emails'));
        \Mail::to($emails)->send(new OrderConfirmationMail($order , $user));

        return response()->json(['message' => 'Emails sent successfully regarding Order ID ' . $orderId]);
    }

    public function filterEmails()
    {
        $emails = collect(["test@example.com", "invalid-email", "another-valid-email@example.org", "one more@test.com"]);

        // Filter the collection using the `filter` method and `filter_var`
        $validEmails = $emails->filter(function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        });

        // Convert the collection back to an array if necessary
        return response()->json($validEmails->all());
    }

    public function addOnOrderUpdate(Request $request)
    {
        try {

            User::where('id' , auth()->id())->update(['edit_order_id' => $request->order_id]);
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
}

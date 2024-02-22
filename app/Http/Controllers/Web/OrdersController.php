<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\OrderConfirmationMail;
use Vanguard\Models\Order;
use Vanguard\Models\ShippingAddress;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class OrdersController extends Controller
{

    public function index(){

        $search = \request()->search;
        $user = \request()->user_id;

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

        if($search){
            $query->where(function ($q) use ($search) {
                $q->orWhere('name', 'like', "%{$search}%");
                $q->orWhere('company', 'like', "%{$search}%");
                $q->orWhere('phone', 'like', "%{$search}%");
                $q->orWhere('shipping_address', 'like', "%{$search}%");
            });
        }

        $count = (clone $query)->count();

        $orders = $query->paginate(100);
        return view('orders.index' , compact('orders','count' , 'user_id' , 'users'));
    }

    public function updateOrder($id, $type){
        #markCompeted, #sendEmail, #delete

        $order = Order::find($id);

        if($type == 'markCompeted'){
            $order->update([
                'is_active' => 0
            ]);
        }
        if($type == 'sendEmail'){
            $user = User::where('id' , $order->user_id)->first();

            \Mail::to($user->email)
                ->cc(['sales@virginfarms.net'])
                ->bcc(['amirseersol@gmail.com'])
                ->send(new OrderConfirmationMail($order , $user));
        }
        if($type == 'delete'){
            $order->delete();
        }

        session()->flash('app_message', 'The order has been updated successfully.');
        return back();

    }
}

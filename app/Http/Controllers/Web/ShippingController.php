<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Models\ShippingAddress;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class ShippingController extends Controller
{

    public function index(){

        $user_id = myRoleName() == 'Admin' ? null : auth()->id(); #for clients show only client but for admin show ALL.

        $query = ShippingAddress::with('user');

        $users = [$user_id => auth()->user()->first_name];

        #client showing and else is in ADMIN.
        if($user_id)
            $query->where('user_id' , $user_id);
        else
            $users = User::where('status' , UserStatus::ACTIVE)
                ->orderby('first_name')
                ->pluck('first_name', 'id')
                ->toArray();

        $addresses = ShippingAddress::with('user')->paginate(100);

        return view('shipping.index' , compact('addresses' , 'user_id' , 'users'));
    }

    public function deleteAddress($id)
    {
        $user_id = myRoleName() == 'Admin' ? null : auth()->id();

        $address = ShippingAddress::find($id);

        if (is_null($user_id))
            $address->delete();

        elseif ($user_id && $address->user_id == $user_id) {
            $address->delete();
        }

        session()->flash('app_message', 'The shipping address has been deleted successfully.');
        return back();
    }


    public function createAndUpdate(Request $request)
    {
        try{

            if ($request->_token) {
                $address = ShippingAddress::create($request->except('_token'));
                session()->flash('app_message', 'The new Shipping Address has been created successfully.');
                return back();
            }

            ShippingAddress::where('id', $request['pk'])->update([$request['name'] => $request['value']]);

            return ['Done'];

        }catch (\Exception $ex){
            session()->flash('app_error', 'Something went wrong plz try again later.');
            return back();
        }









    }

}

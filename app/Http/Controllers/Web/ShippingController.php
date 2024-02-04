<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UsCity;
use Vanguard\Models\UsState;
use Vanguard\Support\Enum\UserStatus;
use Vanguard\User;

class ShippingController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){

        $search = \request()->search;
        $user = \request()->user_id;

        $user_id = myRoleName() == 'Admin' ? null : auth()->id();
        #for clients show only client but for admin show ALL.

        $query = ShippingAddress::with('user');

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
                $q->orWhere('company_name', 'like', "%{$search}%");
                $q->orWhere('phone', 'like', "%{$search}%");
                $q->orWhere('address', 'like', "%{$search}%");
            });
        }

        $addresses = $query->paginate(100);

        $states = UsState::orderby('state_name')
            ->pluck('state_name', 'id')
            ->toArray();

        $states = [null => 'Select State']+$states;

        return view('shipping.index' , compact('addresses' , 'user_id' , 'users' , 'states'));
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
                $data = $request->except('_token');
                if($request->address_id){
                    $address = ShippingAddress::where('id' , $request->address_id)->first();
                    unset($data['address_id']);
                    $address->update($data);
                }
                else{
                    $data['user_id'] = auth()->id();
                    $address = ShippingAddress::create($data);
                }

                session()->flash('app_message', 'The new Shipping Address has been created successfully.');
                return back();
            }

            if ($request->address_id || $request->address_id == 0) {
                User::where('id' , auth()->id())->update(['address_id' => $request->address_id]);
                return ['Done'];
            }

            ShippingAddress::where('id', $request['pk'])->update([$request['name'] => $request['value']]);

            return ['Done'];

        }catch (\Exception $ex){

            dd($ex);
            session()->flash('app_error', 'Something went wrong plz try again later.');
            return back();
        }

    }

    public function loadCities(Request $request){
        $data['cities'] = UsCity::where("state_id", $request->state_id)->get(["city", "id"]);;
        return response()->json($data);
    }

}

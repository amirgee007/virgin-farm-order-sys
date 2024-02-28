<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Carrier;
use Vanguard\User;

class CarriersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display products page page.
     *
     * @return View
     */
    public function index($id = null)
    {
        if($id){
            $carrier = Carrier::find($id);
            $carrier->delete();

            session()->flash('app_message', 'The carrier been deleted successfully.');
            return back();
        }

        $carriers = Carrier::orderBy('carrier_name')->get();
        return view('carriers.index' , compact('carriers'));
    }

    public function createAndUpdate(Request $request)
    {
        try{

            if ($request->carrier_id) {
                User::where('id' , auth()->id())->update(['carrier_id' => $request->carrier_id]);
                auth()->user()->fresh();

                #(new ProductsController())->refreshPriceInCartIfCarrierChange();
                return ['Done'];
            }

            if ($request->_token) {
                $address = Carrier::create($request->except('_token'));
                session()->flash('app_message', 'The new carrier has been created successfully.');
                return back();
            }

            Carrier::where('id', $request['pk'])->update([$request['name'] => $request['value']]);

            return ['Done'];

        }catch (\Exception $ex){
            session()->flash('app_error', 'Something went wrong OR plz try again with unique carrier.');
            return back();
        }

    }
}

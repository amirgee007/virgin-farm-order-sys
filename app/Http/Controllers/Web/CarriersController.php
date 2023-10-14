<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Models\Carrier;

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
    public function index()
    {
        $carriers = Carrier::all();
        return view('carriers.index' , compact('carriers'));
    }

    public function createAndUpdate(Request $request)
    {
        try{
            Carrier::where('id', $request['pk'])->update([$request['name'] => $request['value']]);

            return ['Done'];

        }catch (\Exception $ex){
            session()->flash('app_error', 'Something went wrong plz try again later.');
            return back();
        }









    }
}

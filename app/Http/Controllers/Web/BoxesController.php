<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Box;
use Vanguard\Models\UnitOfMeasure;

class BoxesController extends Controller
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
        $search = \request()->search;

        $query = Box::query();

        if($search){
            $query->where(function ($q) use ($search) {
                $q->orWhere('description', 'like', "%{$search}%");
                $q->orWhere('width', 'like', "%{$search}%");
                $q->orWhere('height', 'like', "%{$search}%");
                $q->orWhere('length', 'like', "%{$search}%");
                $q->orWhere('volume', 'like', "%{$search}%");
                $q->orWhere('weight', 'like', "%{$search}%");
                $q->orWhere('min_value', 'like', "%{$search}%");
            });
        }

        $boxes = $query->paginate(100);

        $unitOfMeasure = UnitOfMeasure::all();
        return view('boxes.index' , compact('boxes' , 'unitOfMeasure'));
    }

    public function deleteBox($id)
    {
        $address = Box::find($id);

        $address->delete();

        session()->flash('app_message', 'The box has been deleted successfully.');
        return back();
    }

    public function createAndUpdate(Request $request)
    {
        try{

            if ($request->_token) {
                $box = Box::create($request->except('_token'));
                session()->flash('app_message', 'The new box has been created successfully.');
                return back();
            }

            Box::where('id', $request['pk'])->update([$request['name'] => $request['value']]);

            return ['Done'];

        }catch (\Exception $ex){

            dd($ex);
            session()->flash('app_error', 'Something went wrong plz try again later.');
            return back();
        }
    }

    public function unitOfMeasuresUpdate(Request $request)
    {
        try {
            UnitOfMeasure::where('id', $request['pk'])->update([$request['name'] => $request['value']]);
            return ['Done'];

        } catch (\Exception $ex) {
        }

    }
}

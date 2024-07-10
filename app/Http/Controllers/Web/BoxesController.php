<?php

namespace Vanguard\Http\Controllers\Web;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Box;
use Vanguard\Models\ProductQuantity;
use Vanguard\Models\Setting;
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

        $start_date = Carbon::now();
        $end_date = Carbon::now()->addDays(6);

        $selected['start'] = $start_date->toDayDateTimeString();
        $selected['end'] = $end_date->toDayDateTimeString();

        $found = Setting::where('key' , 'extra-fees-date')->first();
        if($found){
            $dates = json_decode($found->label , true);

            $selected = [
                'start' => Carbon::parse($dates['date_in'])->toDayDateTimeString(),
                'end' => Carbon::parse($dates['date_out'])->toDayDateTimeString(),
            ];
        }

        if($search){
            $query->where(function ($q) use ($search) {
                $q->orWhere('description', 'like', "%{$search}%");
                $q->orWhere('width', 'like', "%{$search}%");
                $q->orWhere('height', 'like', "%{$search}%");
                $q->orWhere('length', 'like', "%{$search}%");
                $q->orWhere('weight', 'like', "%{$search}%");
                $q->orWhere('min_value', 'like', "%{$search}%");
            });
        }

        $boxes = $query->paginate(100);

        $unitOfMeasure = UnitOfMeasure::all();
        return view('boxes.index' , compact('boxes' , 'unitOfMeasure' , 'selected' , 'found'));
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
            session()->flash('app_error', 'Something went wrong plz try again later.');
            return back();
        }
    }

    public function unitOfMeasuresUpdate(Request $request)
    {
        try {

            if($request->is_adding_new){
                $request->validate([
                    'unit' => 'required|string|max:255',
                    'detail' => 'required|string|max:255',
                    'total' => 'required|integer',
                ]);

                UnitOfMeasure::create([
                    'unit' => $request->unit,
                    'detail' => $request->detail,
                    'total' => $request->total,
                ]);

                return response()->json(['success' => true]);

            }
            else{
                UnitOfMeasure::where('id', $request['pk'])->update([$request['name'] => $request['value']]);
                return ['Done'];
            }

        } catch (\Exception $ex) {
            \Log::error($ex->getMessage() . ' error message during updation of message.');
        }

    }

    public function updateExtraFeesDates(Request $request){

        $dates = dateRangeConverter($request->range);
        $date_in = $dates['date_in'];
        $date_out = $dates['date_out'];

        if ($request->fees == 0) {
            Setting::where('key', 'extra-fees-date')->delete();
        } else {
            $found = Setting::updateOrCreate([
                'key' => 'extra-fees-date'
            ], [
                'value' => $request->fees,
                'label' => json_encode($dates),
                'done_by' => auth()->id(),
            ]);
        }

        session()->flash('app_message', 'Your value,date for extra fees has been updated successfully.');
        return back();
    }
}

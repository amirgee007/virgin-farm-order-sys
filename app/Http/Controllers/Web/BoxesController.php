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

        #for the boxes fees
        $found = Setting::where('key', 'extra-fees-date')->first();

        $selected = $allOthers = $fedex = null;

        if ($found) {

            // ✅ Decode dates (same as before)
            $dates = json_decode($found->label, true);

            $selected = [
                'start' => Carbon::parse($dates['date_in'])->toDayDateTimeString(),
                'end' => Carbon::parse($dates['date_out'])->toDayDateTimeString(),
            ];

            // ✅ NEW: Decode value JSON
            $value = json_decode($found->value, true);

            $allOthers = $value['all_others'] ?? null;
            $fedex = $value['fedex'] ?? null;
        } else {
            $selected = [
                'start' => now()->startOfMonth()->toDayDateTimeString(),
                'end'   => now()->endOfMonth()->toDayDateTimeString(),
            ];
        }


        if ($search) {
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
        #$carriers = getCarriers();

        return view('boxes.index', compact(
            'boxes',
            'unitOfMeasure',
            'selected',
            'found',
            'allOthers',
            'fedex'
        ));
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
        try {

            if ($request->_token) {
                $box = Box::create($request->except('_token'));
                session()->flash('app_message', 'The new box has been created successfully.');
                return back();
            }

            Box::where('id', $request['pk'])->update([$request['name'] => $request['value']]);

            return ['Done'];

        } catch (\Exception $ex) {
            session()->flash('app_error', 'Something went wrong plz try again later.');
            return back();
        }
    }

    public function unitOfMeasuresUpdate(Request $request)
    {
        try {

            if ($request->is_adding_new) {
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

            } else {
                UnitOfMeasure::where('id', $request['pk'])->update([$request['name'] => $request['value']]);
                return ['Done'];
            }

        } catch (\Exception $ex) {
            \Log::error($ex->getMessage() . ' error message during updation of message.');
        }

    }

    public function updateExtraFeesDates(Request $request)
    {
        $dates = dateRangeConverter($request->range);

        // If both are 0 → delete (reset case)
        if ($request->all_others_fee == 0 && $request->fedex_fee == 0) {
            Setting::where('key', 'extra-fees-date')->delete();
        } else {

            $value = [
                'all_others' => (float)$request->all_others_fee,
                'fedex' => (float)$request->fedex_fee,
            ];

            Setting::updateOrCreate(
                ['key' => 'extra-fees-date'],
                [
                    'value' => json_encode($value),
                    'label' => json_encode($dates),
                    'extra_info' => null,
                    'done_by' => auth()->id(),
                ]
            );
        }

        session()->flash('app_message', 'Extra fee dates updated successfully.');
        return back();
    }
}

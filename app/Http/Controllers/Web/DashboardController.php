<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Order;
use Vanguard\Models\ProductQuantity;

class DashboardController extends Controller
{
    /**
     * Displays the application dashboard.
     *
     * @return Factory|View
     */
    public function index()
    {
        if (session()->has('verified')) {
            session()->flash('success', __('E-Mail verified successfully.'));
        }

        $orders = Order::where('is_active' , 1)->orderBy('date_shipped')->limit(10)->get();
        #$future_inventory = ProductQuantity::query()->groupBy(['date_in', 'date_out'])->get();
        $futureInventory = \DB::SELECT('SELECT date_in, date_out, max(updated_at) as updated_at
FROM product_quantities WHERE DATE(date_out) >= CURDATE() GROUP BY date_in, date_out;');

        #$lowInventory = ProductQuantity::where('quantity' , 0)->where('date_out' , '>' , now()->toDateString())->limit(100)->get();
        $lowInventory = [];

        return view('dashboard.index' , compact('orders' , 'futureInventory' , 'lowInventory'));
    }

    public function updateSupplier(Request $request){
        // Store the selected supplier in the user preferences
        $user = auth()->user();
        $user->supplier_id = $request->input('supplier');
        $user->save();

        #, You will be redirected to inventory page.
        return response()->json(['message' => 'Supplier updated successfully.' , 'href' => route('inventory.index')]);
    }

    public function updateFaqRead(Request $request){

        // Store the selected supplier in the user preferences
        auth()->user()->forceFill([
            'announcements_last_read_at' => now()
        ])->save();

        #, You will be redirected to inventory page.
        return response()->json(['message' => 'Settings updated successfully.']);
    }
}

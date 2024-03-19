<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Contracts\View\Factory;
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
        $future_inventory = \DB::SELECT('SELECT date_in, date_out FROM product_quantities WHERE date_out > now() group by date_in,date_out');

        return view('dashboard.index' , compact('orders' , 'future_inventory'));
    }
}

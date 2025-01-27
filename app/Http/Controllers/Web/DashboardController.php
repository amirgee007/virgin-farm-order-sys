<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Order;
use Vanguard\Models\ProductQuantity;
use DB;
use Vanguard\Models\Setting;

// Assuming you are using DB facade

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

        $user_id = myRoleName() == 'Admin' ? null : auth()->id();
        $query = $orders = Order::where('is_active', 1)->orderBy('date_shipped');
        if ($user_id)
            $query->where('user_id', $user_id);

        $orders = $query->limit(10)->get();
        #$future_inventory = ProductQuantity::query()->groupBy(['date_in', 'date_out'])->get();

        $futureInventory = \DB::SELECT('
    SELECT pq.date_in,
           pq.date_out,
           MAX(pq.updated_at) AS updated_at,
           p.supplier_id,
           CASE
               WHEN p.supplier_id = 1 THEN "VF"
               ELSE "Dutch"
           END AS supplier_name
    FROM product_quantities pq
    JOIN products p ON pq.product_id = p.id
    WHERE DATE(pq.date_out) >= CURDATE()
    GROUP BY pq.date_in, pq.date_out, p.supplier_id;
');

        #$lowInventory = ProductQuantity::where('quantity' , 0)->where('date_out' , '>' , now()->toDateString())->limit(100)->get();
        $lowInventory = [];

        return view('dashboard.index', compact('orders', 'futureInventory', 'lowInventory'));
    }

    public function updateSupplier(Request $request)
    {
        // Store the selected supplier in the user preferences
        $user = auth()->user();
        $user->supplier_id = $request->input('supplier');
        $user->save();

        #, You will be redirected to inventory page.
        return response()->json(['message' => 'Supplier updated successfully.', 'href' => route('inventory.index')]);
    }

    public function updateFaqRead(Request $request)
    {

        // Store the selected supplier in the user preferences
        auth()->user()->forceFill([
            'announcements_last_read_at' => now()
        ])->save();

        #, You will be redirected to inventory page.
        return response()->json(['message' => 'Settings updated successfully.']);
    }

    public function checkAdminUploadingFiles()
    {
        // Replace 'your_table' and 'status_column' with your actual table and column
        $status = Setting::where('key', 'admin-uploading')->where('value', 1)->first();

        // Assume status 1 means block interactions, 0 means allow interactions
        if ($status && myRoleName() != 'Admin') {
            return response()->json(['disable' => true]);
        } else {
            return response()->json(['disable' => false]);
        }
    }

}

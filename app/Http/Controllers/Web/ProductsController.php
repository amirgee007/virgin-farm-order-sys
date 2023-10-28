<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Carrier;
use Vanguard\Models\Product;

class ProductsController extends Controller
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
    public function index(Request $request)
    {
        $date_shipped = trim($request->date_shipped);
        $carrier_id = trim($request->carrier_id);
        $purchase_order = trim($request->po);


        $filter = $request->filter;

        $filter = is_array($filter) ? $filter : [];
        $filter = array_filter($filter, function ($a) {
            return trim($a) !== "";
        });

        $query = Product::query();
        if ($date_shipped)
            $query->where('created_at', 'like', "%{$date_shipped}%");


//        if ($date_shipped || $carrier_id || $purchase_order || $filter) {
//            $orders->appends([
//                'search' => $search,
//                'filter' => $filter,
//            ]);
//        }

        $carriers = Carrier::pluck('carrier_name' , 'id')->toArray();
        $products = (clone $query)->paginate(250);
        return view('products.index' ,compact(
            'products',
            'carriers',
            'filter'
        ));
    }

    public function addToCart(){

        $response['result'] = true;
        return response()->json($response);
    }
}

<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
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
    public function index()
    {
        $products = Product::paginate(10);
        return view('products.index' ,compact('products'));
    }

    public function addToCart(){

        $response['result'] = true;
        return response()->json($response);
    }
}

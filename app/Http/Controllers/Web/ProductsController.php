<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\CartDetailMail;
use Vanguard\Models\Carrier;
use Vanguard\Models\Category;
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

        $carriers = Carrier::pluck('carrier_name', 'id')->toArray();
        $products = (clone $query)->paginate(250);
        return view('products.index', compact(
            'products',
            'carriers',
            'filter'
        ));
    }

//    public function addToCart(){
//
//        $response['result'] = true;
//        return response()->json($response);
//    }


    /**
     * Write code on Method
     *
     * @return response()
     */
    public function cart()
    {
        return view('products.cart');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function addToCart(Request $request)
    {

        //  "product_id" => "1"
        //  "mark_code" => null
        //  "quantity" => "123"

        $id = $request->product_id;
        $quantity = $request->quantity;

        $product = Product::findOrFail($id);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->product_text,
                "quantity" => $quantity,
                "price" => $product->unit_price,
                "image" => $product->image_url
            ];
        }

        session()->put('cart', $cart);

        #$response['result'] = true;
        #return response()->json($response);

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            session()->flash('success', 'Product removed successfully');
        }
    }


    public function checkOutCart()
    {

        $carts = session()->get('cart');

        $content = "decreased otherwise increased.";

        \Mail::to('amir@infcompany.com')
            ->cc(['amirseersol@gmail.com'])
            ->send(new CartDetailMail("New Order received PLZ check ", $content));


        session()->put('cart', []);
        session()->flash('success', 'Product removed successfully');
    }

    public function categoriesIndex(){
        $categories = Category::all();

        return view('categories.index', compact(
            'categories'
        ));
    }


}

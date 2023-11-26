<?php

namespace Vanguard\Http\Controllers\Web;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Imports\ImportExcelFiles;
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

    public function inventoryIndex(){

        $query = Product::query();
        $count = Product::query()->count();

        #depend ON date in and date OUT.

        $products = (clone $query)->paginate(100);
        $categories = Category::pluck('description','category_id')->toArray();

        return view('products.inventory.index', compact(
            'products',
            'categories',
            'count'
        ));
    }

    public function uploadProducts(Request $request){

        Storage::put('temp/import_products.xlsx', file_get_contents($request->file('file_products')->getRealPath()));
        $products = Excel::toArray(new ImportExcelFiles(), storage_path('app/temp/import_products.xlsx'));

        #0 Item Class,	1Item No., 2Description,	3UOM	4Price 1, 5Price 3,6Price 5, 7Weight, 8Size
        if (isset($products[0]))
            foreach ($products[0] as $index => $row) {

                try {

                    if ($index < 2) continue;

                    if($index == 250)  break;

                    $data = [
                        'category_id' => trim($row[0]),
                        'item_no' => trim($row[1]),
                        'product_id' => rand(100, 999999),
                        'product_text' => trim($row[2]),
                        'unit_of_measure' => trim($row[3]),

                        'price_fedex' => trim($row[4]),
                        'price_fob' => trim($row[5]),
                        'price_hawaii' => trim($row[6]),

                        'weight' => trim($row[7]),
                        'size' => trim($row[8]),

                    ];

                    Product::updateOrCreate(['item_no' => trim($row[1])],$data); #as for now no specific requirments for the adding product if not found. also no history etc

                } catch (\Exception $exception) {
                    Log::error('Error during inventory import ' . $exception->getMessage());
                    dd($exception->getMessage(), $data);
                }
            }

        session()->flash('app_message', 'Inventory file has been imported in the system.');
        return back();
    }

    public function uploadInventory(Request $request){

        $date_in = Carbon::parse($request->date_in)->toDateString();
        $date_out = Carbon::parse($request->date_out)->toDateString();

        Storage::put('temp/import_inventory.xlsx', file_get_contents($request->file('file_inventory')->getRealPath()));
        $products = Excel::toArray(new ImportExcelFiles(), storage_path('app/temp/import_inventory.xlsx'));

        #0 ITEM_ID #1 ITEM_DESC #2 PRICE_1  #3 PRICE_2	#4 PRICE_3	#5 QUANTITY
        if (isset($products[0]))
            foreach ($products[0] as $index => $row) {

                try {

                    if ($index < 2) continue;

                    $product = Product::where('item_no', trim($row[0]))->first();

                    $data = [
                        'price_fedex' => trim($row[2]),
                        'price_fob' => trim($row[3]),
                        'price_hawaii' => trim($row[4]),
                        'quantity' => $row[5] ? $row[5] : 0,
                        'date_in' => $date_in,
                        'date_out' => $date_out,
                    ];

                    if ($product) {
                        $product->update($data);

                    } else {
                        $data['item_no'] = trim($row[0]);
                        $data['product_text'] = trim($row[1]);
                        $data['product_id'] = rand(100, 999999);

                        #Product::create($data); #as for now no specific requirments for the adding product if not found. also no history etc
                    }

                } catch (\Exception $exception) {
                    Log::error('Error during inventory import ' . $exception->getMessage());
                    dd($exception->getMessage(), $data);
                }
            }

        session()->flash('app_message', 'Inventory file has been imported in the system.');
        return back();
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
        $categories = Category::pluck('description', 'category_id')->toArray();
        $products = (clone $query)->paginate(250);
        return view('products.index', compact(
            'products',
            'carriers',
            'categories',
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

    public function updateCategory(Request $request)
    {
        try {
            Category::where('id', $request['pk'])->update([$request['name'] => $request['value']]);
            return ['Done'];

        } catch (\Exception $ex) {
            Log::error('Edit category error.' . $ex->getMessage());
        }
    }




}

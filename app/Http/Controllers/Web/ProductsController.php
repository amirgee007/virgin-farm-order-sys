<?php

namespace Vanguard\Http\Controllers\Web;

use App\Imports\ProductSkuListImport;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Imports\ImportExcelFiles;
use Vanguard\Mail\CartDetailMail;
use Vanguard\Models\Box;
use Vanguard\Models\Carrier;
use Vanguard\Models\Category;
use Vanguard\Models\Product;
use Vanguard\Models\ProductQuantity;
use Vanguard\Models\ShippingAddress;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:products.manage', ['only' => [
            'updatePurchasePrice',
        ]]);
    }

    public function inventoryIndex(Request $request)
    {
        $date_shipped = trim($request->date_shipped);
        $category_id = trim($request->category);
        $searching = trim($request->searching);

        $address = auth()->user()->shipAddress;

        $query = Product::join('product_quantities', 'product_quantities.product_id', '=', 'products.product_id')->where('quantity', '>' ,0);
        if ($date_shipped){
            $query->whereRaw('"'.$date_shipped.'" between `date_in` and `date_out`');
        }

        if ($category_id){
            $query->where('category_id' , $category_id);
        }

        if ($searching) {
            $query->where(function ($q) use ($searching) {
                $q->orWhere('products.item_no', 'like', "%{$searching}%");
                $q->orWhere('product_text', 'like', "%{$searching}%");
                $q->orWhere('unit_of_measure', 'like', "%{$searching}%");
            });
        }

        $carriers = getCarriers();
        $categories = Category::pluck('description', 'category_id')->toArray();
        $products = (clone $query)->paginate(250);

        if ($date_shipped || $category_id || $searching ) {
            $products->appends([
                'date_shipped' => $date_shipped,
                'searching' => $searching,
                'category' => $category_id,
            ]);
        }

        $priceCol = getPrices()[auth()->user()->price_list];

        return view('products.inventory.index', compact(
            'products',
            'carriers',
            'categories',
            'address',
            'priceCol'
        ));
    }

    public function cart()
    {
        return view('products.inventory.cart');
    }

    public function addToCart(Request $request)
    {

        //  "product_id" => "1"
        //  "mark_code" => null
        //  "quantity" => "123"

        $id = $request->product_id;
        $quantity = $request->quantity;


        $product = Product::findOrFail($id);
        $productInfo = $product->prodQty->first(); #need to check which product qty need to be get OR store id somehwere

        $priceCol = getPrices()[auth()->user()->price_list];

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $cart[$id]['quantity']+$quantity;
        } else {
            $cart[$id] = [
                "name" => $product->product_text,
                "quantity" => $quantity,
                "price" => $productInfo ? $productInfo->$priceCol : 0,
                "image" => $product->image_url,
                "size" => $product->size,
            ];
        }


        session()->put('cart', $cart);

        #$response['result'] = true;
        #return response()->json($response);

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            session()->flash('success', 'Cart updated successfully');
        }
    }

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

    public function deleteProduct($id)
    {
        #todo: plz check all relational data here.
        Product::where('id', $id)->delete();
        session()->flash('success', 'Product deleted successfully');
        return back();
    }


    public function indexManageProducts()
    {

        $query = Product::query();
        $search = \Request::get('search');

        #Search by Item, Description

        #depend ON date in and date OUT.

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('item_no', 'like', "%{$search}%");
                $q->orWhere('product_text', 'like', "%{$search}%");
            });
        }

        $products = (clone $query)->paginate(100);
        $categories = Category::pluck('description', 'category_id')->toArray();

        $count = (clone $query)->count();

        #will check if need to change it
        $start_date = Carbon::now();
        $end_date = Carbon::now()->addDays(5);
        $selected['start'] = $start_date->toDayDateTimeString();
        $selected['end'] = $end_date->toDayDateTimeString();

        $itemsHaveImage = Product::whereNotNull('image_url')->pluck('item_no');
        return view('products.index', compact(
            'products',
            'categories',
            'count',
            'selected',
            'itemsHaveImage'
        ));
    }

    public function uploadProducts(Request $request)
    {

        Storage::put('temp/import_products.xlsx', file_get_contents($request->file('file_products')->getRealPath()));
        $products = Excel::toArray(new ImportExcelFiles(), storage_path('app/temp/import_products.xlsx'));

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes
        #0 Item Class,	1Item No., 2Description,	3UOM	4Price 1, 5Price 3,6Price 5, 7Weight, 8Size
        if (isset($products[0]))
            foreach ($products[0] as $index => $row) {

                try {

                    if ($index < 2) continue;

                    $data = [
                        'category_id' => trim($row[0]),
                        'item_no' => trim($row[1]),
                        'product_id' => rand(100, 999999),
                        'product_text' => trim($row[2]),
                        'unit_of_measure' => trim($row[3]),

                        'price_fob' => trim($row[4]), #price 1
                        'price_fedex' => trim($row[5]), #price 3
                        'price_hawaii' => trim($row[6]), #price 5

                        'weight' => trim($row[7]),
                        'size' => trim($row[8]),

                    ];

                    Product::updateOrCreate(['item_no' => trim($row[1])], $data); #as for now no specific requirments for the adding product if not found. also no history etc

                } catch (\Exception $exception) {
                    Log::error('Error during inventory import ' . $exception->getMessage() . ' and line ' . $exception->getLine());

                    session()->flash('app_error', 'Inventory file has some error please check with admin or upload correct file.');
                    return back();
                }
            }

        session()->flash('app_message', 'Inventory file has been imported in the system.');
        return back();
    }

    public function productUpdateColumn(Request $request)
    {
        try {

            ProductQuantity::where('id', $request['pk'])->update([$request['name'] => $request['value']]);
            return ['Done'];

        } catch (\Exception $ex) {
            #dd($ex->getMessage());
            session()->flash('app_error', 'Something went wrong plz try again later inventoryUpdateColumn.');
            return back();
        }
    }

    public function uploadInventory(Request $request)
    {

       $dates =  dateRangeConverter($request->range);

        $date_in = $dates['date_in'];
        $date_out = $dates['date_out'];

        Storage::put('temp/import_inventory.xlsx', file_get_contents($request->file('file_inventory')->getRealPath()));
        $products = Excel::toArray(new ImportExcelFiles(), storage_path('app/temp/import_inventory.xlsx'));

        #0 ITEM_ID #1 ITEM_DESC #2 PRICE_1  #3 PRICE_2	#4 PRICE_3	#5 QUANTITY
        if (isset($products[0]))
            foreach ($products[0] as $index => $row) {

                try {

                    if ($index < 2) continue;

                    $product = Product::where('item_no', trim($row[0]))->first();

                    $data = [
                        'product_id' => $product->product_id,
                        'item_no' => $product->item_no,
                        'price_fedex' => trim($row[2]),
                        'price_fob' => trim($row[3]),
                        'price_hawaii' => trim($row[4]),

                        'quantity' => $row[5] ? $row[5] : 0,
                        'date_in' => $date_in,
                        'date_out' => $date_out,
                    ];

                    if ($product) {
                        ProductQuantity::updateOrCreate([
                            'product_id' => $product->product_id,
                            'item_no' => $product->item_no,
                            'date_in' => $date_in,
                            'date_out' => $date_out,
                        ], $data);

                    } else {
                        $data['item_no'] = trim($row[0]);
                        $data['product_text'] = trim($row[1]);
                        $data['product_id'] = rand(100, 999999);

                        #Product::create($data); #as for now no specific requirments for the adding product if not found. also no history etc
                    }

                } catch (\Exception $exception) {
                    Log::error('Error during inventory import ' . $exception->getMessage());
                }
            }

        session()->flash('app_message', 'Inventory file has been imported in the system.');
        return back();
    }

    public function uploadProductImages(Request $request)
    {
        try {

//            ini_set('max_execution_time', 18000);
//
//            ini_set('max_execution_time', 300000); //300 seconds = 5 minutes
//            ini_set('max_memory_limit', -1); //300 seconds = 5 minutes
//            ini_set('memory_limit', '4096M');

            $v = Validator::make($request->all(), [
                'images_zip' => 'required|mimes:zip',
            ]);

            $userId = \auth()->id();
            $token = date('dMy-His');

            if ($v->fails()) {
                Log::error("images_zip seems like with wrong way data..");
                return back()->withErrors($v);
            } else {

                $namePathZIP = self::getStorageBackupPath('imagesZip', '.zip');
                Storage::put($namePathZIP, file_get_contents($request->file('images_zip')->getRealPath()));

                Log::emergency('uploadProductImages Started and file saved public/images zip');

                $file = new Filesystem;
                #$file->cleanDirectory("images");

                $zip = new \ZipArchive();
                $file = $request->file('images_zip');

                if ($zip->open($file->path()) === TRUE) {
                    $zip->extractTo("images/$token");
                    $zip->close();
                } else {
                    $zip->close();
                    Log::error("System error UNABLE TO READ the zip file.");
                    return Redirect::back()->withErrors('Your imported ZIP file is invalid please try again.');
                }

                $path = public_path("images/$token");
                $files = File::allFiles($path);

                foreach ($files as $counter => $file) {
                    $url = url("images/$token") . '/' . $file->getFilename();

                    $sku = $file->getFilenameWithoutExtension();

                    $product = Product::where('item_no', trim($sku))->first();

                    if ($product)
                        $product->update(['image_url' => $url]);
                    else
                        unlink($file->getRealPath());
                }
            }

            return Redirect::back()->withMessage('Your zip file and images has been updated and attached.');

        } catch (\Exception $ex) {
            Log::error("Your imported excel file is invalid please try again uploading images " . $ex->getMessage() . '-' . $ex->getLine());
            return Redirect::back()->withErrors('Your imported excel file is invalid please try again.');
        }
    }

    public function copyImageToOtherProduct(Request $request){

        $productToo = Product::where('id', $request->item_copy_too)->first();
        $productFrom = Product::where('item_no', $request->item_copy_from)->first();

        if($productFrom && $productToo && $productFrom->image_url){
            $productToo->image_url = $productFrom->image_url;
            $productToo->save();

            session()->flash('app_message', 'Product Image has been copied successfully.');
        }
        else
            session()->flash('app_error', 'Product not found in the system plz write correct and try again.');

        return back();
    }
    public function iventoryReset(){

        ProductQuantity::query()->whereDate('date_out' , '<' , now()->toDateString())->update([
            'quantity' => 0,
            'date_in' => null,
            'date_out' => null,
        ]);

        session()->flash('app_message', 'Inventory has been reset successfully.');
        return back();
    }

    public function resetSpecificInventory(Request $request){

        $dates =  dateRangeConverter($request->range);
        $date_in = $dates['date_in'];
        $date_out = $dates['date_out'];

        $query = ProductQuantity::query()
            ->whereDate('date_in' , $date_in)
            ->whereDate('date_out' , $date_out);

        if($request->flag == 'delete')
            $query->delete();
        else
            $query->update([
                'quantity' => 0,
                'date_in' => null,
                'date_out' => null,
            ]);

        session()->flash('app_message', 'Your selected inventory has been updated successfully.');
        return back();
    }

    public static function getStorageBackupPath($for, $ext = '.xls')
    {

        $user = '-by-' . auth()->user()->name;

        $now = now()->toDateTimeString();
        $folder = 'public/backups/' . $for . '/' . now()->year . '/' . strtolower(now()->format('M')) . '/';

        return $folder . \Str::slug($now) . $user . $ext;
    }

    /**
     * Display products page page.
     *
     * @return View
     */


//    public function addToCart(){
//
//        $response['result'] = true;
//        return response()->json($response);
//    }

    public function categoriesIndex()
    {
        $categories = Category::latest()->get();
        #->orderBy('description' , 'desc')

        return view('categories.index', compact(
            'categories'
        ));
    }

    public function categoriesDelete($id = null)
    {
        if($id){
            #plz check we dont have any other links categories etc
            Category::where('id' , $id)->delete();
            session()->flash('app_message', 'Your Category has been deleted successfully.');

            return back();
        }
    }

    public function updateCategory(Request $request)
    {
        try {
            if ($request->category_name) {
                $found = Category::where('description', $request->category_name)->first();
                if (!$found){
                    $last = Category::whereNotNull('category_id')->orderBy('category_id' , 'desc')->first();

                    Category::create([
                        'description' => $request->category_name,
                        'category_id' => $last->category_id+1,
                    ]);
                }
                session()->flash('app_message', 'Your Category has been added successfully.');
                return back();
            }

            Category::where('id', $request['pk'])->update([$request['name'] => $request['value']]);
            return ['Done'];

        } catch (\Exception $ex) {
            Log::error('Edit category error.' . $ex->getMessage());
        }
    }


}

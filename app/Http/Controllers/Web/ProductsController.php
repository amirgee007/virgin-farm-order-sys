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
use Vanguard\Mail\OrderConfirmationMail;
use Vanguard\Models\Box;
use Vanguard\Models\Carrier;
use Vanguard\Models\Category;
use Vanguard\Models\Order;
use Vanguard\Models\OrderItem;
use Vanguard\Models\Product;
use Vanguard\Models\ProductQuantity;
use Vanguard\Models\Setting;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UnitOfMeasure;

class ProductsController extends Controller
{

    public $dateIn = null, $dateOut = null;

    public function __construct()
    {
        $this->middleware('auth');

        $this->middleware('permission:products.manage', ['only' => [
            'indexManageProducts',
        ]]);
    }

    public function inventoryIndex(Request $request)
    {
        $date_shipped = trim($request->date_shipped);
        $category_id = trim($request->category);
        $searching = trim($request->searching);
        $user = auth()->user();
        $address = $user->shipAddress;

        if (!$date_shipped) {
            $date_shipped = $user->last_ship_date;
        } else {
            if ($date_shipped) {
                $user->update([
                    'last_ship_date' => $date_shipped
                ]);
            }
        }

        if ($user->edit_order_id) {
            $order = Order::find($user->edit_order_id);
            $date_shipped = $order->date_shipped ?? $date_shipped;
        }

        #ALTER TABLE `users` ADD `last_ship_date` DATE NULL DEFAULT NULL AFTER `address_id`;
        $query = Product::join('product_quantities', 'product_quantities.product_id', '=', 'products.id')
            ->leftJoin('carts', 'carts.product_id', '=', 'products.id')
            ->where('product_quantities.quantity', '>', 0);

        // Apply conditions based on the supplier_id and for the special products
        if (in_array($user->supplier_id, [1, 2])) {
            $query->where('products.supplier_id', $user->supplier_id);
        } elseif ($user->supplier_id == 3) {
            $query->where('product_quantities.is_special', 1);
        }

        $query->distinct('products.id');

        $highlightedDates = $this->getHighlightedDates($query);

        if ($date_shipped) {
            $query->whereRaw('"' . $date_shipped . '" between `date_in` and `date_out`');
        } else {
            $query->where('product_quantities.quantity', '<', 0); #just to ignore will make it zero after testing
        }

        if ($category_id) {
            $query->where('category_id', $category_id);
        }

        if ($searching) {
            $catIds = Category::where('description', 'like', "%{$searching}%")
                ->pluck('category_id')
                ->toArray();

            $query->where(function ($q) use ($searching, $catIds) {
                $q->where('products.item_no', 'like', "%{$searching}%")
                    ->orWhere('product_text', 'like', "%{$searching}%")
                    ->orWhere('unit_of_measure', 'like', "%{$searching}%");

                if (!empty($catIds)) {
                    $q->orWhereIn('products.category_id', $catIds);
                }
            });
        }

        $carriers = getCarriers($user->state > 52 ? 1 : 0);

        $categoriesQuery = Category::query();
        $dutchCats = Category::dutchCategories(); #ok new logic

        #categories based on the SUPPLIER selected
        if ($user->supplier_id == 2) {
            $categoriesQuery->whereIn('category_id', $dutchCats);
        } else {
            $categoriesQuery->whereNotIn('category_id', $dutchCats);
        }

        $categories = $categoriesQuery->orderBy('description')->pluck('description', 'category_id')->toArray();

        $products = (clone $query)->groupBy('products.id')
            ->orderBy('category_id') // Sort by category_id first
            ->orderBy('product_text') // Then sort by product_text within the same category
            ->selectRaw('supplier_id,category_id,product_quantities.id as p_qty_id,product_quantities.is_special, products.id as id, product_text, image_url, unit_of_measure, products.stems, product_quantities.quantity - COALESCE(SUM(carts.quantity), 0) as quantity, weight, products.size, price_fob, price_fedex, price_hawaii')
            ->paginate(100);

        $fixed = [
            0 => 'New Order',
            1 => 'Add-On General',
        ];
        $myOrders = $fixed + Order::where('user_id', auth()->id())
                ->whereDate('date_shipped', '>', now()->toDateString())
                ->where('is_active', 1)
                ->latest()
                ->pluck('id', 'id')
                ->toArray();

        if ($date_shipped || $category_id || $searching) {
            $products->appends([
                'date_shipped' => $date_shipped,
                'searching' => $searching,
                'category' => $category_id,
            ]);
        }

        $priceCol = myPriceColumn();

        #need to make it in auto job and show some counter + time etc
        CartController::makeCartEmptyIfTimePassed();

        return view('products.inventory.index', compact(
            'products',
            'carriers',
            'categories',
            'address',
            'priceCol',
            'date_shipped',
            'user',
            'myOrders',
            'highlightedDates'
        ));
    }

    public function getHighlightedDates($query)
    {
        $highlightedDates = [];

        $productQuantities = $query->select('date_in', 'date_out')
            ->whereDate('date_out', '>=', Carbon::today())
            ->get();

        foreach ($productQuantities as $productQuantity) {
            $period = \Carbon\CarbonPeriod::create($productQuantity->date_in, $productQuantity->date_out);
            foreach ($period as $date) {
                $highlightedDates[] = $date->format('Y-m-d');
            }
        }
        // Ensure the dates are unique
        return array_unique($highlightedDates);
    }

    public function deleteProduct($id)
    {
        #todo: plz check all relational data here.
        Product::where('id', $id)->delete();
        session()->flash('success', 'Product deleted successfully');
        return back();
    }

    public function resetProduct($id)
    {
        Product::where('id', $id)->update([
            'image_url' => null
        ]);
        session()->flash('success', 'Product image has ben removed.');
        return back();
    }


    public function indexManageProducts()
    {
        $filters = [
            0 => 'Filter/Sort Products',
            1 => 'Sorty by item A-Z',
            2 => 'Sorty by item Z-A',
            3 => 'Products with images',
            4 => 'Products without images',
        ];

        $query = Product::query();
        $search = \Request::get('search');
        $category = \Request::get('category');
        $filter = \Request::get('filter');
        $qty_found = \Request::get('qty_found');

        $date_in = \Request::get('date_in');
        $date_out = \Request::get('date_out');
        $supp = \Request::get('supp') ?? 1;
        #depend ON date in and date OUT.

        if ($date_in && $date_out) {
            $query->join('product_quantities', 'products.id', '=', 'product_quantities.product_id')
                ->whereDate('product_quantities.date_in', '>=', $date_in)
                ->whereDate('product_quantities.date_out', '<=', $date_out)
                ->where('products.supplier_id', $supp);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('item_no', 'like', "%{$search}%");
                $q->orWhere('product_text', 'like', "%{$search}%");
            });
        }

        if ($category) {
            $query->where(function ($q) use ($category) {
                $q->orWhere('category_id', 'like', $category);
            });
        }

        if ($filter) {
            if ($filter == 1) #a-z
                $query->orderBy('item_no', 'ASC');
            elseif ($filter == 2) #z-a
                $query->orderBy('item_no', 'DESC');
            elseif ($filter == 3) #with images
                $query->whereNotNull('image_url');
            elseif ($filter == 4) #without images
                $query->whereNull('image_url');
        }

        $products = (clone $query)->select('products.*')->paginate(100);

        if ($filter || $search || $category || $qty_found || $date_in || $date_out || $supp) {
            $products->appends([
                'filter' => $filter,
                'search' => $search,
                'category' => $category,
                'date_in' => $date_in,
                'date_out' => $date_out,
                'supp' => $supp,
            ]);
        }

        $categories = Category::pluck('description', 'category_id')->toArray();
        $count = (clone $query)->count();

        #will check if need to change it
        $start_date = Carbon::now();
        $end_date = Carbon::now()->addDays(5);
        $selected['start'] = $start_date->toDayDateTimeString();
        $selected['end'] = $end_date->toDayDateTimeString();

        $itemsHaveImage = Product::whereNotNull('image_url')->pluck('item_no');
        $columnCustomNames = getReportColumns();

        return view('products.index', compact(
            'products',
            'categories',
            'count',
            'selected',
            'itemsHaveImage',
            'filters',
            'columnCustomNames',
        ));
    }

    public function createNewProduct(Request $request)
    {

        $product = Product::where('item_no', $request->item_no)->first();

        $data = $request->except('_token');
        if ($product) {
            $product->update($data);
        } else
            Product::create($data);

        session()->flash('app_message', 'Product has been created in the system.');
        return back();
    }

    public function uploadCreateProducts(Request $request)
    {
        #products master file.
        Storage::put('temp/import_products.xlsx', file_get_contents($request->file('file_products')->getRealPath()));
        $products = Excel::toArray(new ImportExcelFiles(), storage_path('app/temp/import_products.xlsx'));

        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        $UOM = UnitOfMeasure::pluck('total', 'unit')->toArray();

        #0 Item Class,	1Item No., 2Description,	3UOM	4Price 1, 5Price 3,6Price 5, 7Weight, 8Size
        if (isset($products[0]))
            foreach ($products[0] as $index => $row) {

                try {

                    if ($index < 2) continue;
                    $uomTrim = trim($row[3]);
                    $data = [
                        'item_no' => trim($row[1]),
                        'category_id' => trim($row[0]),
                        'product_text' => trim($row[2]),
                        'unit_of_measure' => $uomTrim,
                        'stems' => isset($UOM[$uomTrim]) ? $UOM[$uomTrim] : 1, #for our own purpose we are doing.

                        'weight' => trim($row[7]),
                        'size' => trim($row[8]),
                    ];

                    $def_price_fob = trim($row[4]);
                    $def_price_fedex = trim($row[5]);
                    $def_price_hawaii = trim($row[6]);

                    #because the AS400 must have at least .01 so we consodure it as zero in these cases.
                    $prices = [
                        'def_price_fob' => ($def_price_fob == '' || $def_price_fob == 0.01) ? 0 : $def_price_fob,
                        'def_price_fedex' => ($def_price_fedex == '' || $def_price_fedex == 0.01) ? 0 : $def_price_fedex,
                        'def_price_hawaii' => ($def_price_hawaii == '' || $def_price_hawaii == 0.01) ? 0 : $def_price_hawaii,
                    ];

                    $catsDutch = Category::dutchCategories(); #ok new logic

                    $data['supplier_id'] = 1;

                    if (in_array($data['category_id'], $catsDutch)) {
                        $data['supplier_id'] = 2;
                    }

                    $product = Product::where('item_no', trim($row[1]))->first();

                    if ($product) {
                        #USED not this BUT save mater file price here in ths table and use default prices.
                        foreach ($prices as $key => $value) {
                            if ((float)$value <= 0) {
                                unset($prices[$key]);
                            }
                        }

                        if ($prices)
                            $product->update($prices);

                        $product->update($data);
                    } else {
//                      $data['product_id']  = rand(100, 999999);
                        $product = Product::create($data); #as for now no specific requirments for the adding product if not found. also no history etc
                    }

                    #need to check if here we can also need to put an extra inventory or not.
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

    private function excelSerialDateToDate($serial)
    {
        $excelBaseDate = \DateTime::createFromFormat('Y-m-d', '1899-12-30');
        return $excelBaseDate->add(new \DateInterval('P' . $serial . 'D'))->format('Y-m-d');
    }

    private function excelSerialTimeToTime($serial)
    {
        // Check if it's a time serial (less than 1)
        if ($serial < 1) {
            $totalSeconds = $serial * 86400; // Convert the fraction of the day to seconds
            $hours = floor($totalSeconds / 3600);
            $minutes = floor(($totalSeconds % 3600) / 60);
            // Create a formatted string for AM/PM
            return date('H:i', strtotime("$hours:$minutes"));
        }

        // If it's a date serial, handle it differently (not in scope here)
        return null;
    }

    public function uploadInventory(Request $request)
    {
        ini_set('max_execution_time', 18000);

        // Reset and delete zero quantity products
        \DB::statement("DELETE FROM `product_quantities` WHERE `quantity` = 0");
        $missing = [];

        // Handle file upload
        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'required|file|mimes:xls,xlsx|max:10008', // 10MB Max
            ]);

            updateSystemStatus(1);
            $excel = $request->file('file');
            $filenamePut = 'extraBulk/' . uniqid() . '.' . $excel->getClientOriginalExtension();
            $filenameRead = 'app/' . $filenamePut;

            try {
                Storage::put($filenamePut, file_get_contents($excel->getRealPath()));
                $products = Excel::toArray(new ImportExcelFiles(), storage_path($filenameRead));

                $expiredtime = null;

                foreach ($products[0] as $index => $row) {
                    if ($index == 1) {
                        $this->dateIn = $this->excelSerialDateToDate($row['3']);
                        $this->dateOut = $this->excelSerialDateToDate($row['5']);

                        $expiredtime = $this->excelSerialTimeToTime($row['6']);
                    }

                    if ($index > 5) {
                        $this->processProductRow($row, $expiredtime, $missing, $request->is_special);
                    }
                }

                if ($missing) {
                    $this->sendMissingItemEmail($missing);
                }

                updateSystemStatus(0);

                $this->sendEmailIfPriceNotCorrect();
                Log::info($this->dateIn . ' date in and date out BULK imported successfully ' . $this->dateOut . ' uploaded BY ' . auth()->user()->first_name);
                return response()->json(['message' => 'File uploaded and imported successfully'], 200);

            } catch (\Exception $ex) {
                Log::warning('Error during bulk files upload, please check: ' . $ex->getMessage() . ' on line ' . $ex->getLine());
                updateSystemStatus(0);
                return response()->json(['error' => 'Invalid Format File.'], 500);
            }
        } else {
            return $this->handleSingleFileUpload($request, $missing);
        }
    }

    private function processProductRow($row, $expiredtime, &$missing, $isSpecial = false)
    {
        $cleaned_string = str_replace(",", "", trim($row[0])); // Cleaned SKU

        // Ensure the SKU is not empty
        if (empty($cleaned_string)) {
            Log::warning("Skipping row due to empty or invalid SKU.");
            return;
        }

        $product = Product::where('item_no', $cleaned_string)->first(); // Check if product exists

        if ($product) {
            $data = $this->buildProductData($row, $product); // Build product data
            if ($expiredtime) {
                $data['expired_at'] = $expiredtime;
                Log::debug($expiredtime);
            }
            if ($isSpecial) {
                $data['is_special'] = 1;
                Log::notice($product->item_no . ' becomes special now for date ' . $this->dateIn . ' to ' . $this->dateOut);
            }

            // Update or create product quantity
            ProductQuantity::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'item_no' => $product->item_no,
                    'date_in' => $this->dateIn,
                    'date_out' => $this->dateOut,
                ],
                $data
            );
        } else {
            // Log the missing product and add it to the missing array
            $missing[] = $cleaned_string;
            Log::warning("Product with item_no: $cleaned_string not found.");
        }
    }


    private function buildProductData($row, $product)
    {
        $data = [
            'product_id' => $product->id,
            'item_no' => $product->item_no,
            'quantity' => $row[5] ? trim($row[5]) : 0,
            'date_in' => $this->dateIn,
            'date_out' => $this->dateOut,
            'price_fedex' => $product->def_price_fedex,
            'price_fob' => $product->def_price_fob,
            'price_hawaii' => $product->def_price_hawaii,
        ];

        // Conditionally add prices to the data array if they are greater than zero
        if (floatval(trim($row[2])) > 0) {
            $data['price_fedex'] = trim($row[2]);
        }
        if (floatval(trim($row[3])) > 0) {
            $data['price_fob'] = trim($row[3]);
        }
        if (floatval(trim($row[4])) > 0) {
            $data['price_hawaii'] = trim($row[4]);
        }

        return $data;
    }

    private function handleSingleFileUpload(Request $request, &$missing)
    {
        updateSystemStatus(1);
        $dates = dateRangeConverter($request->range);
        $date_in = $dates['date_in'];
        $date_out = $dates['date_out'];

        Storage::put('temp/import_inventory.xlsx', file_get_contents($request->file('file_inventory')->getRealPath()));
        $products = Excel::toArray(new ImportExcelFiles(), storage_path('app/temp/import_inventory.xlsx'));

        if (isset($products[0])) {
            foreach ($products[0] as $index => $row) {
                if ($index == 0) continue; // Skip headings
                $expiredtime = $request->expired_at;
                $this->dateIn = $date_in;
                $this->dateOut = $date_out;
                $this->processProductRow($row, $expiredtime, $missing, $request->is_special);
            }
        }

        if ($missing) {
            $this->sendMissingItemEmail($missing);
        }
        $this->sendEmailIfPriceNotCorrect();

        updateSystemStatus(0);
        session()->flash('app_message', 'Inventory file has been imported into the system.');
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
                    $sku = trim($sku);

                    if (strlen($sku) <= 3) continue;

                    $product = Product::where('item_no', $sku)->first();

                    if ($product)
                        $product->update(['image_url' => $url]);
                    else {
                        #check here if products have any other with descriton mathced then we can check and updated here.
                        #i.e plz check with chritst here and then if all ok then make that logic as corrected.
                        $products = Product::where('product_text', 'like', "%{$sku}%")->get();
                        if (count($products) > 0 && strlen($sku) >= 4) {
                            foreach ($products as $product) {
                                $product->update(['image_url' => $url]);
                            }
                        } else
                            unlink($file->getRealPath());
                    }
                }
            }

            return Redirect::back()->withMessage('Your zip file and images has been updated and attached.');

        } catch (\Exception $ex) {
            Log::error("Your imported excel file is invalid please try again uploading images " . $ex->getMessage() . '-' . $ex->getLine());
            return Redirect::back()->withErrors('Your imported excel file is invalid please try again.');
        }
    }

    public function getIndexDetailsAjax(Request $request)
    {

        try {

            $array = explode(',', $request->ids);

            $data = Product::whereIn('id', $array)->pluck('image_url', 'id')->toArray();
            $data['success'] = true;

            return response()->json($data);

        } catch (\Exception $exc) {
            Log::error('getIndexDetailsAjax error plz check why .' . $exc->getMessage());

            $data['success'] = false;
            return response()->json($data);
        }
    }

    public function copyImageToOtherProduct(Request $request)
    {

        #load_img_modal
        if ($request->load_img_modal) {

            $haveImages = Product::whereNotNull('image_url')->orderBy('item_no')->pluck('item_no', 'id')->toArray();
            $noImages = Product::whereNull('image_url')->orderBy('item_no')->pluck('item_no', 'id')->toArray();

            $view = view('products._partial._copy_img_modal', compact('haveImages', 'noImages'))->render();
            $response['modal'] = $view;

            return response()->json($response);
        } elseif ($request->source && $request->targets) {
            $productSource = Product::where('id', $request->source)->first();

            if ($productSource)
                Product::whereIn('id', $request->targets)->update([
                    'image_url' => $productSource->image_url
                ]);

            session()->flash('app_message', 'Multiple products image has been copied successfully.');
        } else {
            $productToo = Product::where('id', $request->item_copy_too)->first();
            $productFrom = Product::where('item_no', $request->item_copy_from)->first();

            if ($productFrom && $productToo && $productFrom->image_url) {
                $productToo->image_url = $productFrom->image_url;
                $productToo->save();

                session()->flash('app_message', 'Product Image has been copied successfully.');
            } else
                session()->flash('app_error', 'Product not found in the system plz write correct and try again.');
        }

        return back();
    }

    public function iventoryReset()
    {

        ProductQuantity::query()->update([
            'quantity' => 0,
            'date_in' => null,
            'date_out' => null,
            'is_special' => 0,
        ]);

        session()->flash('app_message', 'Inventory has been reset successfully.');
        return back();
    }

    public function iventorySyncFromFTP()
    {
        #reset and delete zero qty products
        $haveComma = \DB::statement("DELETE FROM `product_quantities` WHERE `quantity` = 0 ORDER BY `quantity` ASC");

        $files = Storage::disk('local')->files('extra');
        $countBefore = count($files);

        $missing = [];
        foreach ($files as $file) {
            // Read each file as an Excel file
            $path = storage_path('app\\' . $file);

            try {
                $products = \Excel::toArray(new ImportExcelFiles(), $path);

                foreach ($products[0] as $index => $row) {

                    if ($index == 1) {
                        // Convert Excel serial dates to PHP dates
                        $this->dateIn = $this->excelSerialDateToDate($row[3]);
                        $this->dateOut = $this->excelSerialDateToDate($row[5]);
                    }

                    // Process each product row
                    if ($index > 5) { // Assuming the first row is headers
                        $product = Product::where('item_no', trim($row[0]))->first();

                        if ($product) {

                            $data = [
                                'product_id' => $product->id,
                                'item_no' => $product->item_no,
                                'quantity' => $row[5] ? trim($row[5]) : 0,
                                'date_in' => $this->dateIn,
                                'date_out' => $this->dateOut,
                                'price_fedex' => $product->def_price_fedex,
                                'price_fob' => $product->def_price_fob,
                                'price_hawaii' => $product->def_price_hawaii,
                            ];

                            // Update prices if provided in the file and greater than zero
                            if (floatval(trim($row[2])) > 0) {
                                $data['price_fedex'] = trim($row[2]);
                            }
                            if (floatval(trim($row[3])) > 0) {
                                $data['price_fob'] = trim($row[3]);
                            }
                            if (floatval(trim($row[4])) > 0) {
                                $data['price_hawaii'] = trim($row[4]);
                            }

                            // Create or update product quantities
                            ProductQuantity::updateOrCreate([
                                'product_id' => $product->id,
                                'item_no' => $product->item_no,
                                'date_in' => $this->dateIn,
                                'date_out' => $this->dateOut,
                            ], $data);

                        } else
                            $missing[] = $row[0];
                    }
                }
            } catch (\Exception $e) {
                Log::error('Failed to import data from Excel file: ' . $e->getMessage() . ' file name is ' . $file);
                continue; // Continue processing the next file
            }

            // After processing the file, delete it
            Storage::delete($file);
            --$countBefore;
        }

        if ($countBefore > 0) {
            $fileWord = $countBefore === 1 ? 'file' : 'files'; // Handle singular/plural
            $message = "Total $countBefore $fileWord remaining to sync. All others done.";
            Log::info("Sync status: $countBefore $fileWord remaining."); // Logging the information
        } else {
            $message = "All files have been successfully synced."; // Logging complete sync
        }

        if ($missing)
            $this->sendMissingItemEmail($missing);

        return response()->json(['message' => $message], 200);
    }

    public function resetSpecificInventory(Request $request)
    {

        $dates = dateRangeConverter($request->range);
        $date_in = $dates['date_in'];
        $date_out = $dates['date_out'];

        $query = ProductQuantity::query()
            ->whereDate('date_in', $date_in)
            ->whereDate('date_out', $date_out);

        #1 = only virgin, 2= dutch and 3 = special
        if (in_array($request->flower_type, [1, 2, 3])) {

            if ($request->flower_type == 3)
                $query->where('product_quantities.is_special', 1);
            else {
                $query->whereHas('product', function ($subQuery) use ($request) {
                    $subQuery->where('products.supplier_id', $request->flower_type);
                })->where('product_quantities.is_special', 0);
            }
        }

        if ($request->flag == 'delete') {
            $query->delete();
        } else {
            $query->update([
                'quantity' => 0,
                'date_in' => null,
                'date_out' => null,
                'is_special' => 0,
            ]);
        }

        if ($request->flag == 'delete')
            $query->delete();
        else
            $query->update([
                'quantity' => 0,
                'date_in' => null,
                'date_out' => null,
                'is_special' => 0,
            ]);

        session()->flash('app_message', 'Your selected inventory has been reset/deleted successfully.');
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


    public function categoriesIndex()
    {
        $categories = Category::latest()->get();

        return view('categories.index', compact(
            'categories',
        ));
    }

    public function categoriesDelete($id = null)
    {
        if ($id) {
            $products = Product::where('category_id', $id)->get();

            if (count($products) > 0) {
                session()->flash('app_error', 'Sorry, you cannot delete this category because it is linked to some products.');
                return back();
            }

            #plz check we dont have any other links categories etc
            Category::where('id', $id)->delete();
            session()->flash('app_message', 'Your Category has been deleted successfully.');
            return back();
        }
    }

    public function updateCategory(Request $request)
    {
        try {
            if ($request->category_name) {
                $found = Category::where('description', $request->category_name)->first();
                if (!$found) {
                    $last = Category::whereNotNull('category_id')->orderBy('category_id', 'desc')->first();

                    Category::create([
                        'description' => $request->category_name,
                        'category_id' => $last->category_id + 1,
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

    public function sendMissingItemEmail($items)
    {
        #$cleaned_string = str_replace(",", "", trim($row[0]));

        try {
            $content = "Items from inventory file are not present in the master file. Please update and reload the files." . implode(',', $items);

            $subj = count($items) . ' Missing Items in Master File';
            \Mail::raw($content, function ($message) use ($subj) {
                $message->to([
                    'esteban@virginfarms',
                    'angief@virginfarms.com',
                    'weborders@virginfarms.com',
                    'amirseersol@gmail.com'
                ])->subject($subj);
            });

        } catch (\Exception $ex) {
            Log::error(implode(',', $items) . ' itesm list sendMissingItemEmail plz check ASAP.' . $ex->getMessage());
        }

    }

    public function sendEmailIfPriceNotCorrect()
    {
        $itemNos = ProductQuantity::where('quantity', '>', 0)
            ->where(function ($query) {
                $query->whereBetween('price_fob', [0, 0.29])
                    ->orWhereBetween('price_fedex', [0, 0.29])
                    ->orWhereBetween('price_hawaii', [0, 0.29]);
            })->pluck('item_no')
            ->toArray();

        try {
            $content = "Some items prices are too low please check asap. " . implode(',', $itemNos);

            if ($itemNos)
                \Mail::raw($content, function ($message) {
                    $message->to(['esteban@virginfarms', 'weborders@virginfarms.com', 'angief@virginfarms.com'
                    ])->subject('Items from inventory file are added with wrong price < 0.29');
                });

        } catch (\Exception $ex) {
            Log::error(implode(',', $items) . ' itesm list sendMissingItemEmail plz check ASAP.' . $ex->getMessage());
        }

    }
}

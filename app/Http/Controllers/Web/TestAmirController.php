<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Vanguard\Exports\ProductReportExport;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\VirginFarmGlobalMail;
use Vanguard\Mail\VirginFarmsSystemMail;
use Vanguard\Models\Box;
use Vanguard\Models\OrderItem;
use Vanguard\Models\Product;
use Vanguard\Models\ProductQuantity;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UnitOfMeasure;
use Vanguard\Services\MailchimpService;
use Vanguard\User;

class TestAmirController extends Controller
{

    public function index3()
    {

        $user = User::query()->inRandomOrder()->first();

        dd($user);
        // Define additional fields such as company, birthday, etc.
        $mergeFields = [
            'FNAME' => $user->first_name, // Assuming you have a first_name field in your User model
            'LNAME' => $user->last_name,  // Assuming you have a last_name field in your User model
            'COMPANY' => $user->company,  // Assuming you have a company field
            'BIRTHDAY' => $user->birthday // Assuming you have a birthday field (in format MM/DD)
        ];

        // Add user to Mailchimp audience
        $mailchimpService = new MailchimpService();
        $mailchimpService->addSubscriber($user->email, ['Web Shop Users'], $mergeFields);

        dd('DONE');
        return view('test');
    }

    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'columns' => 'required|array',
            'date_in' => 'required|date',
            'date_out' => 'required|date|after_or_equal:date_in',
            'report_type' => 'required|in:pdf,excel',
        ]);

        $dateIn = $validated['date_in'];
        $dateOut = $validated['date_out'];
        $columns = $validated['columns'];

        $columnCustomNames = getReportColumns();

        // Add table names to the columns
        $columnsWithTableNames = array_map(function ($column) {
            if (in_array($column, ['product_text', 'item_no'])) {
                return "products.$column";
            } else {
                return "product_quantities.$column";
            }
        }, $columns);

        // Fetch data
        $data = ProductQuantity::where('quantity', '>', 0)
            ->where('date_in', '>=', $dateIn)
            ->where('date_out', '<=', $dateOut)
            ->join('products', 'products.id', '=', 'product_quantities.product_id')
            ->join('categories', 'categories.category_id', '=', 'products.category_id') // Join with categories table
            ->orderBy('products.category_id') // Sort by category_id
//            ->orderBy('products.product_text') // Then sort by product_text
            ->get(array_merge($columnsWithTableNames, ['categories.description as category_name'])); // Include category_name in the result

        $groupedData = $data->groupBy('category_name');

        $name = 'Inventory-Report-'.$dateIn;
        if ($validated['report_type'] === 'excel') {
            return \Excel::download(new ProductReportExport($columns , $groupedData , $columnCustomNames), "$name.xlsx");
            #return \Excel::download(new ProductReportExport($data, $columns , $columnCustomNames), );
        } else {
            #return view('products.report', compact('data', 'columns' , 'dateIn' , 'columnCustomNames'));
            $pdf = \Pdf::loadView('products.reports.report', compact('columns' , 'dateIn' , 'columnCustomNames' ,'groupedData'));

            return $pdf->download("$name.pdf");
        }
    }

    public function index2($id = 9)
    {

        ini_set('max_execution_time', 300000); //300 seconds = 5 minutes
        ini_set('max_memory_limit', -1); //300 seconds = 5 minutes
        ini_set('memory_limit', '4096M');

        dd();

        dd($user);

        #current size before method callings is: 111.41
        # cart:931 current size and next max limit is: -66.41 18

        $request = Request::create('/', 'GET', ['selection' => 90.73 + 18]);

        $ok = (new CartController())->validateCartSelection($request);

        dd($ok);
        $user = User::first();
        $content = '<p>New user has been successfully registered on Virgin farms order system. Here are the details of the new user:</p>'
            . '<ul>'
            . '<li><strong>Full Name:</strong> ' . $user->first_name . ' ' . $user->last_name . '</li>'
            . '<li><strong>Last Name:</strong> ' . $user->last_name . '</li>'
            . '<li><strong>Company Name:</strong> ' . $user->company_name . '</li>'
            . '<li><strong>Phone No:</strong> ' . $user->phone . '</li>'
            . '<li><strong>Email:</strong> ' . $user->email . '</li>'
            . '<li><strong>Username:</strong> ' . $user->username . '</li>'
            . '<li><strong>Sales Representative:</strong> ' . $user->sales_rep . '</li>'
            . '<li><strong>Shipping Address:</strong> ' . $user->address . '</li>'
            . '<li><strong>Appt/Suite:</strong> ' . $user->apt_suit . '</li>'
            . '<li><strong>City:</strong> ' . $user->city . '</li>'
            . '<li><strong>State:</strong> ' . $user->state . '</li>'
            . '<li><strong>Zip:</strong> ' . $user->zip . '</li>'
            . '<li><strong>Shipping Method:</strong> ' . $user->ship_method . '</li>'
            . '</ul>';

        \Mail::to('amirseersol@gmail.com')
            ->cc('amirseersol@gmail.com')
            ->send(new VirginFarmGlobalMail('New User Registration Notification', $content));

        dd('ddd');

        $user_id = 9;
        $order_id = rand();
        $message = 'abccccccccccccccc adfsa fasdfareacasd';

        addNotification($user_id, $order_id, $message);
        dd($quantity);

        $user = User::first();
        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $content = 'User Changed his shipping address under profile page please check asap i.e user is ' . $user->first_name;

        \Mail::raw($content, function ($message) use ($salesRepEmail) {
            $message->to('christinah@virginfarms.com')
                ->bcc(['amirseersol@gmail.com', $salesRepEmail])
                ->subject('Hi, plz check some user updated shipping address');
        });

//        \Mail::to('christinah@virginfarms.com')
//            ->bcc('amirseersol@gmail.com')
//            ->send(new VirginFarmGlobalMail('Hi, plz check some user updated shipping address', $content));

        dd('dd');

        dd($ranges);

        foreach ($ranges as $range) {
            #not in current ranges
            if ($currentSelection >= $range['min'] && $currentSelection <= $range['max']) {
                $max = $range['max'] + 1;
            }
        }

        return response()->json([
            'valid' => true,
            'nextMax' => $max
        ]);

        $ranges = Box::pluck('max_value', 'min_value')->toArray();

        $adjustedRanges = [];
        $startLimit = 1;

        foreach ($ranges as $min => $max) {

            if ($startLimit > $min - 1) {
                $startLimit = $max + 1;
                continue;
            }

            $adjustedRanges[] = ['min' => $startLimit, 'max' => $min - 1]; // Add the adjusted range to the array
            $startLimit = $max + 1;
        }
    }
}

<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\VirginFarmGlobalMail;
use Vanguard\Mail\VirginFarmsSystemMail;
use Vanguard\Models\Box;
use Vanguard\Models\OrderItem;
use Vanguard\Models\Product;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UnitOfMeasure;
use Vanguard\User;

class TestAmirController extends Controller
{

    public function index2($value = 0){

        #current size before method callings is: 111.41
        # cart:931 current size and next max limit is: -66.41 18


        $request = Request::create('/', 'GET', ['selection' => 135 ]);

        $ok = (new CartController())->validateCartSelection($request);


        dd($ok);
        $user = User::first();
        $content = '<p>New user has been successfully registered on Virgin farms order system. Here are the details of the new user:</p>'
            . '<ul>'
            . '<li><strong>Full Name:</strong> ' . $user->first_name.' '.$user->last_name . '</li>'
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

        addNotification($user_id , $order_id , $message);
        dd($quantity);

        $user = User::first();
        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $content = 'User Changed his shipping address under profile page please check asap i.e user is '.$user->first_name;

        \Mail::raw($content, function ($message) use($salesRepEmail) {
            $message->to('christinah@virginfarms.com')
                ->bcc(['amirseersol@gmail.com' , $salesRepEmail])
                ->subject('Hi, plz check some user updated shipping address');});

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

            $adjustedRanges[] = [ 'min' => $startLimit, 'max' => $min - 1 ]; // Add the adjusted range to the array
            $startLimit = $max + 1;
        }
    }
}

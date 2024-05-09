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

        $prices = [
            'def_price_fob' => 30, #price 1
            'def_price_fedex' => -10, #price 3
            'def_price_hawaii' => 3, #price 5
        ];

        foreach ($prices as $key => $value){
            if ((float)$value <= 0) {
                unset($prices[$key]);
            }
        }

        dd($prices);

        $size = 13.45;# 18


        $input = $size;
        $sizeHere = $currentSelection = $input > 45 ? 45 - $input : $input;

        $ranges = Box::pluck('max_value', 'min_value')->toArray();
        $nextMinimumNeeded = null;

        $response = [];
        // Check if the quantity matches any of the current ranges
        foreach ($ranges as $min => $max) {
            if ($sizeHere >= $min && $sizeHere <= $max) {
                // Quantity matches a range
                $response =  ['valid' => true, 'size' => $sizeHere, 'nextMax' => null];
                break;
            }
            // Find the smallest 'next minimum' number that is larger than the quantity
            if ($sizeHere < $min && ($nextMinimumNeeded === null || $min < $nextMinimumNeeded)) {
                $nextMinimumNeeded = $min;
            }

            // Return the result indicating no match and the next minimum number needed
            $response =  ['valid' => false, 'size' => $sizeHere, 'nextMax' => $nextMinimumNeeded];
        }

        dd($response);




        $quantity = 21.5;


        $user = User::first();
        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

        $content = 'User Changed his shipping address under profile page please check asap i.e user is '.$user->first_name;

        \Mail::raw($content, function ($message) use($salesRepEmail) {
            $message->to('christinah@virginfarms.com')
                ->bcc(['amir@infcompany.com' , $salesRepEmail])
                ->subject('Hi, plz check some user updated shipping address');});

//        \Mail::to('christinah@virginfarms.com')
//            ->bcc('amir@infcompany.com')
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

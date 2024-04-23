<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Box;
use Vanguard\Models\OrderItem;
use Vanguard\Models\Product;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UnitOfMeasure;
use Vanguard\User;

class TestAmirController extends Controller
{

    public function index2($value = 0){

        $quantity = 21.5;

        $ranges = Box::pluck('max_value', 'min_value')->toArray();
        $nextMinimumNeeded = null;

        // Check if the quantity matches any of the current ranges
        foreach ($ranges as $min => $max) {

            if ($quantity >= $min && $quantity <= $max) {
                // Quantity matches a range
                return ['match' => true, 'quantity' => $quantity, 'nextMinimumNeeded' => null];
            }

            // Find the smallest 'next minimum' number that is larger than the quantity
            if ($quantity < $min && ($nextMinimumNeeded === null || $min < $nextMinimumNeeded)) {
                $nextMinimumNeeded = $min;
            }
        }

        // Return the result indicating no match and the next minimum number needed
        return ['match' => false, 'quantity' => $quantity, 'nextMinimumNeeded' => $nextMinimumNeeded];


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

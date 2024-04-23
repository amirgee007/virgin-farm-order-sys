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

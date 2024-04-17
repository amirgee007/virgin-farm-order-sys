<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\OrderItem;
use Vanguard\Models\Product;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UnitOfMeasure;
use Vanguard\User;

class TestAmirController extends Controller
{

    public function index2($value = 0){


        $UOM = UnitOfMeasure::pluck('total' , 'unit')->toArray();

        $prods = OrderItem::all();

        foreach($prods as $prod){

            $prod->stems = @$UOM[$prod->stems];
            $prod->save();

        }

        dd('ddd');
        if ($result !== null) {
            echo "$valueToCheck is within the valid range.";
        } else {
            echo "$valueToCheck is not within the valid range.";
        }

    }
}

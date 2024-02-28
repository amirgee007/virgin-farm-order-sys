<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Models\Product;
use Vanguard\Models\ShippingAddress;
use Vanguard\User;

class TestAmirController extends Controller
{

    public function index2($value = 0){

        $carts = session()->get('cart');
        $cart2 = [];

        foreach ($carts as $id => $details) {
            $product = Product::where('id', $id)->first();
            $productInfo = $product->prodQty->first(); #need to check which product qty need to be get OR store id somehwere

            $priceCol = myPriceColumn();

            if ($productInfo) {
                $cart2[$id] = [
                    "name" => $details['name'],
                    "item_no" => @$details['item_no'],
                    "quantity" => $details['quantity'],
                    "price" => $productInfo ? $productInfo->$priceCol : $details['price'],
                    "image" => $product->image_url,
                    "size" => $details['size'],
                    "stems" => $details['stems'],
                    "max_qty" => $productInfo->quantity,
                    "time" => now()->toDateTimeString(),
                ];
            }
        }

        dd($carts , $cart2);

        session()->put('cart', []);
        session()->put('cart', $cart2);

        dd(implode(', ' ,$result));
        if ($result !== null) {
            echo "$valueToCheck is within the valid range.";
        } else {
            echo "$valueToCheck is not within the valid range.";
        }

    }
}

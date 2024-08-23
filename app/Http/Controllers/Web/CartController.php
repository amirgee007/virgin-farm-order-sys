<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\OrderConfirmationMail;
use Vanguard\Models\Box;
use Vanguard\Models\Cart;
use Vanguard\Models\Order;
use Vanguard\Models\OrderItem;
use Vanguard\Models\Product;
use Vanguard\Models\ProductQuantity;
use Vanguard\Models\ShippingAddress;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function saveOrderNotes(Request $request){
        $time = now()->addMinutes(10);
        \Cache::put('order_note_' . auth()->id(), $request->notes, $time);
    }
    public function viewCart()
    {
        #need to make it in auto job and show some counter + time etc
        CartController::makeCartEmptyIfTimePassed();

        $string = 'order_note_' .auth()->id();
        \Cache::forget($string);

        $carts = getMyCart();
        #Similar to the Solé web shop, we would like a time-out session timer. After 1 hour,
        #if the customer does not checkout, the items in the cart are emptied back to inventory.
        return view('products.inventory.cart' , compact('carts'));
    }

    public function addToCart(Request $request)
    {

        $quantity = $request->quantity;
        $product_qty_id = $request->p_qty_id;

        $productQty = ProductQuantity::find($product_qty_id);

        $product = Product::where('id', $productQty->product_id)->first();

        #need to chekc it should add the correct id that we select at main page.
        #$productInfo = $product->prodQty->first(); #need to check which product qty need to be get OR store id somehwere

        $priceCol = myPriceColumn();

        $cartExist = Cart::mineCart()->where('item_no' , $product->item_no)->first();

        if($cartExist){
            $cartExist->increment('quantity' , $quantity);
        }
        else
        {
            Cart::create([
                "product_qty_id" => $product_qty_id,
                "product_id" => $productQty->product_id,
                "item_no" => $product->item_no,
                "name" => $product->product_text,
                "quantity" => $quantity,
                "price" => $productQty ? $productQty->$priceCol : 1,
                "image" => $product->image_url,
                "size" => $product->size,
                "stems" => $product->stems,
                "max_qty" => $productQty->quantity,
                "user_id" => auth()->id(),
            ]);
        }

        #$response['result'] = true;
        #return response()->json($response);

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function refreshPriceInCartIfCarrierChange()
    {
        try {
            $priceCol = myPriceColumn();

            Log::notice('refreshPriceInCartIfCarrierChange called and updated plz check it. '.$priceCol);
            $carts = getMyCart();
            $cart2 = [];

            foreach ($carts as $details) {

                $product = Product::where('id', $details->product_id)->first();
                $productInfo = Product::where('id', $details->product_qty_id)->first();

                if ($productInfo) {
                    $details->update([
                        "price" => $productInfo ? $productInfo->$priceCol : 1, # $details->price
                        "image" => $product->image_url,
                        "max_qty" => $productInfo->quantity,
                    ]);
                }
            }

        } catch (\Exception $exc) {
            Log::error($exc->getMessage() . ' error in the refreshPriceInCartIfCarrierChange ' . $exc->getLine() . ' User:' . auth()->id());
        }
    }

    public function updateCartQty(Request $request)
    {

        if ($request->id && $request->quantity) {
            $cartExist = Cart::mineCart()->where('id' , $request->id)->first();

            $existProduct = checkAvailableQty($cartExist->product_id);

            if($existProduct && $existProduct->quantity >= $request->quantity ){
                $cartExist->quantity = $request->quantity;
                $cartExist->save();
                session()->flash('app_message', 'Your cart quantity updated successfully.');
            }
            else
                session()->flash('app_error', 'Sorry, We dont have more available product quantity.');
        }
    }

    public function remove(Request $request)
    {
        if ($request->id) {
            Cart::mineCart()->where('id' , $request->id)->delete();
        }
        session()->flash('success', 'Product removed successfully from cart.');

        return back();
    }

    public function emptyCart(Request $request)
    {
        Cart::mineCart()->delete();

        auth()->user()->update([
            'edit_order_id' => null
        ]);

        auth()->user()->fresh();

        session()->flash('success', 'Your all products removed from cart successfully.');

        return back();
    }

    public function checkOutCart()
    {

        $user = $shipAddress = itsMeUser();

        $carts = getMyCart();
        $address_id = $user->address_id; #if empty/ZERO then default address will be user not others.
        if($address_id)
            $shipAddress = ShippingAddress::find($address_id);

        $date_shipped = $user->last_ship_date;
        $carrier_id = $user->carrier_id;

        $size = $is_add_on = 0; #just to show on edit page as its ADD ON not added first time.
        $full_add_on = $user->edit_order_id > 0 ? min(2, $user->edit_order_id) : 0; #here we put 1 full OR 2 is half

        if($user->edit_order_id > 1){ #because 1 is add-on new order
            $order = Order::find($user->edit_order_id);
            $total = $order->total;
            $is_add_on = 1;
        }
        else{
            $total = 0;
            $order = Order::create([
                'user_id' => $user->id,
                'sales_rep' => $user->sales_rep,
                'full_add_on' => $full_add_on, #we will use it on cart and if needed will show on orders page later on.
                'date_shipped' => $date_shipped,
                'carrier_id' => $carrier_id,
                'name' => $shipAddress->name,
                'email_address' => $shipAddress->email,
                'company' => $shipAddress->company_name,
                'phone' => $shipAddress->phone,
                'shipping_address' => $shipAddress->address,
                'address_2' => $shipAddress->city_name.' ,'.$shipAddress->state_name.' ,'.$shipAddress->zip, #all others stuff city, state, and zip
            ]);
        }

        foreach ($carts as $cart){
            $productQty = ProductQuantity::where('id', $cart->product_qty_id)->first();
            $product = Product::where('id', $cart->product_id)->first();

            if($productQty){
                $productQty->decrement('quantity', $cart->quantity); #TODO if we need STOCK history change
                $product->increment('sold' , $cart->quantity);
            }

            $total += $cart->price * $cart->quantity * $cart->stems;
            $size += $cart->size * $cart->quantity;

            $item = [
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'item_no' => @$cart->item_no,
                'name' => $cart->name,
                'quantity' => $cart->quantity,
                'price' => round2Digit($cart->price),
                'size' => $cart->size,
                'is_add_on' => $is_add_on,
                'stems' => $product->stems,
                'sub_total' => round2Digit($cart->price * $cart->quantity * $cart->stems),
            ];

            OrderItem::create($item);
        }

        $totalCubeTax = getCubeSizeTax($size);
        $totalWithTax = $total + $totalCubeTax;

        $string = 'order_note_' .$user->id;
        $notes = \Cache::get($string);
        \Cache::forget($string);

        #sub_total	discount	tax		totalen
        $order->update([
            'sub_total' => round2Digit($total),
            'discount' => 0,
            'tax' => 0,
            'shipping_cost' => $totalCubeTax,
            'full_add_on' => $order->full_add_on == 0 ? $full_add_on : $order->full_add_on,
            'total' => round2Digit($totalWithTax),
            'notes' => $notes,
        ]);

        $order->refresh();
        #$content = "decreased otherwise increased."; E-Commerce Checkout - Virgin Farms Inc. / PB W831718

        Log::info($order->id . ' placed the order like this with total and sub total '.$order->total);

        #Mario  weborders@virginfarms.com will add later
        $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);
        #if(config('app.env') != 'local')
        \Mail::to($user->email)
            ->cc('weborders@virginfarms.com')
            ->bcc([
                'info@virginfarms.com',
                'sales@virginfarms.com',
                'christinah@virginfarms.com',
                'esteban@virginfarms.com',
                'sales@virginfarms.net', $salesRepEmail
            ])->send(new OrderConfirmationMail($order , $user));

        #client notify
        $message = 'Your order has been successfully received.';
        addOwnNotification($message , $order->id , $user->id);

        #admin notify
        $message = 'New Order Received: WO-'.$order->id;
        addOwnNotification($message , $order->id);

        Cart::mineCart()->delete();

        auth()->user()->update([
            'edit_order_id' => null
        ]);

        auth()->user()->fresh();

        session()->flash('success', 'Your order has been successfully received. We will notify you shortly. Please check your email for the order summary.');

        return \redirect(route('orders.index'));
    }


    public static function makeCartEmptyIfTimePassed()
    {
        try {

            if (cartTimeLeftSec() <= 0) { #it means 1 hour left so remove all those
                #\Log::info($user->username . ' users cart has been removed due to last hour, plz keep an eye on it. ' . $cart->updated_at->toDateTimeString());
                Cart::where('user_id', auth()->id())->delete();
            }

        } catch (\Exception $ex) {
            \Log::error($ex->getMessage() . ' something went wrong here plz check for this use,...! ' . auth()->id());
        }
    }

    public function validateCartSelection(Request $request)
    {

        $user = auth()->user(); // Assuming this fetches the current authenticated user

        if ($user->edit_order_id) {
            return response()->json(['valid' => true, 'size' => 1, 'nextMax' => 1]);
        }

        $input = $request->input('selection');

        $sizeHere = $input > 45 ? $input % 45 : $input;

        $ranges = Box::get(['min_value', 'max_value'])->pluck('max_value', 'min_value')->toArray();
        $nextMinimumNeeded = null;
        $response = ['valid' => false, 'size' => $sizeHere, 'nextMax' => null];

        foreach ($ranges as $min => $max) {
            if ($sizeHere >= $min && $sizeHere <= $max) {
                $response = ['valid' => true, 'size' => $sizeHere, 'nextMax' => $sizeHere];
                break;
            }
            if ($sizeHere < $min && ($nextMinimumNeeded === null || $min < $nextMinimumNeeded)) {
                $nextMinimumNeeded = $min;
            }
        }

        if (!$response['valid']) {
            $response['nextMax'] = $nextMinimumNeeded;
        }

        return response()->json($response);
    }

}

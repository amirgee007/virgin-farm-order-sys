<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Mail\OrderConfirmationMail;
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

//        $this->middleware('permission:products.manage', ['only' => [
//            'updatePurchasePrice',
//        ]]);
    }

    public function cart()
    {

        $carts = session()->get('cart');
        #Similar to the Solé web shop, we would like a time-out session timer. After 1 hour,
        #if the customer does not checkout, the items in the cart are emptied back to inventory.

        return view('products.inventory.cart' , compact('carts'));
    }

    public function addToCart(Request $request)
    {

        //  "id" => "1"
        //  "mark_code" => null
        //  "quantity" => "123"

        $id = $request->id;

        $quantity = $request->quantity;

        $product = Product::where('id', $id)->first();
        $productInfo = $product->prodQty->first(); #need to check which product qty need to be get OR store id somehwere

        $priceCol = myPriceColumn();

        $cart = session()->get('cart', []);

        $stems = $product->stemsCount ? $product->stemsCount->total : 1;

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $cart[$id]['quantity']+$quantity;
        } else {
            $cart[$id] = [
                "name" => $product->product_text,
                "item_no" => $product->item_no,
                "quantity" => $quantity,
                "price" => $productInfo ? $productInfo->$priceCol : 0,
                "image" => $product->image_url,
                "size" => $product->size,
                "stems" => $product->stemsCount ? $product->stemsCount->total : 1,
                "max_qty" => $productInfo->quantity,
                "time" => now()->toDateTimeString(),
            ];
        }

        session()->put('cart', $cart);

        #$response['result'] = true;
        #return response()->json($response);

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function refreshPriceInCartIfCarrierChange()
    {
        try {

            $priceCol = myPriceColumn();

            Log::notice('refreshPriceInCartIfCarrierChange called and updated plz check it. '.$priceCol);
            $carts = session()->get('cart');
            $cart2 = [];

            if(is_null($carts)) return '';

            foreach ($carts as $id => $details) {
                $product = Product::where('id', $id)->first();
                $productInfo = $product->prodQty->first(); #need to check which product qty need to be get OR store id somehwere

                if ($productInfo) {
                    $cart2[$id] = [
                        "name" => $details['name'],
                        "item_no" => @$details['item_no'],
                        "quantity" => $details['quantity'],
                        "price" => $productInfo ? $productInfo->$priceCol :0, # $details['price']
                        "image" => $product->image_url,
                        "size" => $details['size'],
                        "stems" => $details['stems'],
                        "max_qty" => $productInfo->quantity,
                        "time" => now()->toDateTimeString(),

                    ];
                }
            }

            session(['cart' => $cart2]);

//            session()->put('cart', []);
//            session()->put('cart', $cart2);
//            session()->save();

        } catch (\Exception $exc) {
            Log::error($exc->getMessage() . ' error in the refreshPriceInCartIfCarrierChange ' . $exc->getLine() . ' User:' . auth()->id());
        }
    }

    public function update(Request $request)
    {
        #$product = Product::where('id', $id)->first();
        #$productInfo = $product->prodQty->first(); #need to check which product qty need to be get OR store id somehwere

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
        }
        session()->flash('success', 'Product removed successfully');

        return back();
    }

    public function emptyCart(Request $request)
    {
        session()->put('cart', []);
        session()->flash('success', 'Your all products removed from cart successfully.');

        return back();
    }

    public function checkOutCart()
    {
        $user = $shipAddress = itsMeUser();;

        $carts = session()->get('cart');
        $address_id = $user->address_id; #if empty/ZERO then default address will be user not others.
        if($address_id)
            $shipAddress = ShippingAddress::find($address_id);

        #"name" => #"company_name" #"phone"  #"address"

        $date_shipped = $user->last_ship_date;
        $carrier_id = $user->carrier_id;

        $order = Order::create([
            'user_id' => $user->id,
            'date_shipped' => $date_shipped,
            'carrier_id' => $carrier_id,
            'name' => $shipAddress->name,
            'company' => $shipAddress->company,
            'phone' => $shipAddress->phone,
            'shipping_address' => $shipAddress->address,
            'address_2' => $shipAddress->city_name.' ,'.$shipAddress->state_name.' ,'.$shipAddress->zip, #all others stuff city, state, and zip
        ]);

        $total = $size = 0;
        $items = [];
        foreach ($carts as $id => $details){
            $productQty = ProductQuantity::where('id',$id)->first();

            if($productQty)
                $productQty->decrement('quantity', $details['quantity']); #TODO if we need STOCK history change

            $total += $details['price'] * $details['quantity'] * $details['stems'];
            $size += $details['size'] * $details['quantity'];

            $item = [
                'order_id' => $order->id,
                'product_id' => $id,
                'item_no' => @$details['item_no'],
                'name' => $details['name'],
                'quantity' => $details['quantity'],
                'price' => round2Digit($details['price']),
                'size' => $details['size'],
                'stems' => $details['stems'],
                'sub_total' => round2Digit($details['price'] * $details['quantity'] * $details['stems']),
            ];

            OrderItem::create($item);
        }

        $totalCubeTax = getCubeSizeTax($size);
        $totalWithTax = $total + $totalCubeTax;

        #sub_total	discount	tax		total
        $order->update([
            'sub_total' => round2Digit($total),
            'discount' => 0,
            'tax' => 0,
            'shipping_cost' => $totalCubeTax,
            'total' => round2Digit($totalWithTax),
        ]);

        $order->refresh();
        #dd($order->items);
        #$content = "decreased otherwise increased."; E-Commerce Checkout - Virgin Farms Inc. / PB W831718

        Log::info($order->id . ' placed the order like this with total and sub total '.$order->total);

        \Mail::to($user->email)
            ->cc(['sales@virginfarms.net'])
            ->bcc(['amirseersol@gmail.com'])
            ->send(new OrderConfirmationMail($order , $user));

        session()->put('cart', []);
        session()->flash('success', 'Your order has been recived successfully. You will be notified soon.');


        return \redirect(route('inventory.index'));
    }

}

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
use Vanguard\Models\PromoCode;
use Vanguard\Models\ShippingAddress;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function saveOrderNotes(Request $request)
    {
        try {
            $time = now()->addMinutes(10);
            \Cache::put('order_note_' . auth()->id(), $request->notes, $time);
        } catch (\Exception $ex) {
            Log::error('Error in saveOrderNotes: ' . $ex->getMessage());
        }
    }

    public function viewCart()
    {
        try {
            CartController::makeCartEmptyIfTimePassed();
            \Cache::forget("order_note_" . auth()->id());

            $carts = getMyCart();

            // Check if this is the user's first order
            $isFirstOrder = Order::where('user_id', auth()->id())->count() < 2;
            $discount_percentage = 0;

            if ($isFirstOrder) {
                $promo = PromoCode::find(1);
                $discount_percentage = $promo ? $promo->discount_percentage : 0;
            }

            return view('products.inventory.cart', compact('carts' , 'discount_percentage'));
        } catch (\Exception $ex) {
            Log::error('Error in viewCart: ' . $ex->getMessage());
        }
    }

    public function addToCart(Request $request)
    {
        try {

            if (!auth()->user()->is_approved) {
                return redirect()->back()->with('success', 'Please ask admin to approve your account!');
            }
            $quantity = $request->quantity;
            $product_qty_id = $request->p_qty_id;

            $productQty = ProductQuantity::find($product_qty_id);
            $product = Product::where('id', $productQty->product_id)->first();

            $priceCol = myPriceColumn();
            $cartExist = Cart::mineCart()->where('item_no', $product->item_no)->first();

            if ($cartExist) {
                $cartExist->increment('quantity', $quantity);
            } else {
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
        } catch (\Exception $ex) {
            Log::error('Error in addToCart: ' . $ex->getMessage());
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function refreshPriceInCartIfCarrierChange()
    {
        try {
            $priceCol = myPriceColumn();

            Log::notice('refreshPriceInCartIfCarrierChange called and updated plz check it. ' . $priceCol);
            $carts = getMyCart();

            foreach ($carts as $details) {
                $product = Product::where('id', $details->product_id)->first();
                $productInfo = Product::where('id', $details->product_qty_id)->first();

                if ($productInfo) {
                    $details->update([
                        "price" => $productInfo ? $productInfo->$priceCol : 1,
                        "image" => $product->image_url,
                        "max_qty" => $productInfo->quantity,
                    ]);
                }
            }
        } catch (\Exception $exc) {
            Log::error('Error in refreshPriceInCartIfCarrierChange: ' . $exc->getMessage() . ' at line ' . $exc->getLine());
        }
    }

    public function updateCartQty(Request $request)
    {
        try {
            if ($request->id && $request->quantity) {
                $cartExist = Cart::mineCart()->where('id', $request->id)->first();
                $existProduct = checkAvailableQty($cartExist->product_id);

                if ($existProduct && $existProduct->quantity >= $request->quantity) {
                    $cartExist->quantity = $request->quantity;
                    $cartExist->save();
                    session()->flash('app_message', 'Your cart quantity updated successfully.');
                } else {
                    session()->flash('app_error', 'Sorry, We dont have more available product quantity.');
                }
            }
        } catch (\Exception $ex) {
            Log::error('Error in updateCartQty: ' . $ex->getMessage());
        }
    }

    public function remove(Request $request)
    {
        try {
            if ($request->id) {
                Cart::mineCart()->where('id', $request->id)->delete();
            }
        } catch (\Exception $ex) {
            Log::error('Error in remove: ' . $ex->getMessage());
        }
        session()->flash('success', 'Product removed successfully from cart.');
        return back();
    }

    public function emptyCart(Request $request)
    {
        try {
            Cart::mineCart()->delete();
            auth()->user()->update(['edit_order_id' => null]);
            auth()->user()->fresh();
        } catch (\Exception $ex) {
            Log::error('Error in emptyCart: ' . $ex->getMessage());
        }
        session()->flash('success', 'Your all products removed from cart successfully.');
        return back();
    }

    public function checkOutCart()
    {
        try {
            $user = $shipAddress = itsMeUser();
            $carts = getMyCart();
            $address_id = $user->address_id;

            if ($address_id) {
                $shipAddress = ShippingAddress::find($address_id);
            }

            $date_shipped = $user->last_ship_date;
            $carrier_id = $user->carrier_id;
            $size = $is_add_on = 0;
            $full_add_on = $user->edit_order_id > 0 ? min(2, $user->edit_order_id) : 0;

            if ($user->edit_order_id > 1) {
                $order = Order::find($user->edit_order_id);
                $total = $order->total;
                $is_add_on = 1;
            } else {
                $total = 0;
                $order = Order::create([
                    'user_id' => $user->id,
                    'sales_rep' => $user->sales_rep,
                    'full_add_on' => $full_add_on,
                    'date_shipped' => $date_shipped,
                    'carrier_id' => $carrier_id,
                    'name' => $shipAddress->name,
                    'email_address' => $shipAddress->email,
                    'company' => $shipAddress->company_name,
                    'phone' => $shipAddress->phone,
                    'shipping_address' => $shipAddress->address,
                    'address_2' => $shipAddress->city_name . ' ,' . $shipAddress->state_name . ' ,' . $shipAddress->zip,
                ]);
            }

            foreach ($carts as $cart) {
                $productQty = ProductQuantity::where('id', $cart->product_qty_id)->first();
                $product = Product::where('id', $cart->product_id)->first();

                if ($productQty) {
                    $productQty->decrement('quantity', $cart->quantity);
                    $product->increment('sold', $cart->quantity);
                }

                $total += $cart->price * $cart->quantity * $cart->stems;
                $size += $cart->size * $cart->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'item_no' => $cart->item_no,
                    'name' => $cart->name,
                    'quantity' => $cart->quantity,
                    'price' => round2Digit($cart->price),
                    'size' => $cart->size,
                    'is_add_on' => $is_add_on,
                    'stems' => $product->stems,
                    'sub_total' => round2Digit($cart->price * $cart->quantity * $cart->stems),
                ]);
            }

            $totalCubeTax = getCubeSizeTax($size);
            $totalWithTax = $total + $totalCubeTax;

            $cacheKey = "order_note_{$user->id}";
            // Retrieve and clear the cached order note in one step
            $notes = \Cache::pull($cacheKey);

            $promoCodeKey = "promo_code_{$user->id}";
            $discountAmountKey = "discount_amount_{$user->id}";

            // Retrieve and clear cached promo code and discount amount
            $promoCodeId = \Cache::pull($promoCodeKey);
            $discountAmount = \Cache::pull($discountAmountKey);

            if ($promoCodeId) {
                $promo = PromoCode::find($promoCodeId);
                if ($promo) {
                    $promo->increment('used_count');
                }
            } else {
                // Check if this is the user's first order
                $isFirstOrder = Order::where('user_id', $user->id)->count() < 2;
                $firstOrderDiscount = 0;

                if ($isFirstOrder) {
                    $promoCodeId = 1;
                    $promo = PromoCode::find($promoCodeId);

                    if ($promo) {
                        $firstOrderDiscount = $promo->amount;
                        $discountAmount = 0;

                        if (!empty($promo->discount_percentage) && $promo->discount_percentage > 0) {
                            $discountAmount = ($promo->discount_percentage / 100) * $total;
                            $promo->increment('used_count');
                        }
                    }
                }
            }

            $order->update([
                'sub_total' => round2Digit($total),
                'discount' => 0,
                'tax' => 0,
                'shipping_cost' => $totalCubeTax,
                'full_add_on' => $order->full_add_on == 0 ? $full_add_on : $order->full_add_on,
                'total' => round2Digit($totalWithTax),
                'notes' => $notes,
                'promo_code_id' => $promoCodeId ?? null,
                'discount_applied' => $discountAmount ?? 0,
            ]);

            $order->refresh();

            Log::info($order->id . ' placed the order with total and sub total ' . $order->total);

            $salesRepEmail = getSalesRepsNameEmail($user->sales_rep);

            \Mail::to($user->email)
                ->cc('weborders@virginfarms.com')
                ->bcc([
                    'info@virginfarms.com',
                    'sales@virginfarms.com',
                    'christinah@virginfarms.com',
                    'esteban@virginfarms.com',
                    'sales@virginfarms.net', $salesRepEmail
                ])->send(new OrderConfirmationMail($order, $user));

            addOwnNotification('Your order has been successfully received.', $order->id, $user->id);
            addOwnNotification('New Order Received: WO-' . $order->id, $order->id);

            Cart::mineCart()->delete();
            auth()->user()->update(['edit_order_id' => null]);
            auth()->user()->fresh();

            session()->flash('success', 'Your order has been successfully received. We will notify you shortly. Please check your email for the order summary.');
            return \redirect(route('orders.index'));
        } catch (\Exception $ex) {
            Log::error('Error in checkOutCart: ' . $ex->getTraceAsString());

            session()->flash('app_error', 'Something went wrong plz check with admin.');
            return back();
        }
    }

    public static function makeCartEmptyIfTimePassed()
    {
        try {
            if (cartTimeLeftSec() <= 0) {
                Cart::where('user_id', auth()->id())->delete();
            }
        } catch (\Exception $ex) {
            Log::error('Error in makeCartEmptyIfTimePassed: ' . $ex->getMessage());
        }
    }

    public function validateCartSelection(Request $request)
    {
        try {
            $user = auth()->user();

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
        } catch (\Exception $ex) {
            Log::error('Error in validateCartSelection: ' . $ex->getMessage());
        }
    }

    public function applyPromoCode(Request $request)
    {

        $carts = getMyCart();
        $cubic_weight = 0;
        foreach ($carts as $cartItem)
            $cubic_weight += $cartItem->size * $cartItem->quantity;

        $userId = auth()->id();
        $request->validate(['promo_code' => 'required|string']);

        #todo: check max user as well as check how many times we need it to use
        #first promo code can only apply on first order.
        $promoCode = PromoCode::where('id', '>', 1)
            ->where('min_box_weight', '<=', $cubic_weight)
            ->where('code', $request->promo_code)
            ->first();

        if (!$promoCode || !$promoCode->isValid()) {
            return response()->json(['message' => 'Invalid or expired promo code.', 'success' => false], 400);
        }

        $totalAmount = $request->total_amount; // Get total order amount from request
        $discountAmount = 0; // Default no discount

        if (!is_null($promoCode->discount_percentage) && $promoCode->discount_percentage > 0) {
            $discountAmount = ($promoCode->discount_percentage / 100) * $totalAmount;
        }

        // Calculate new total after discount
        $newTotal = max(0, $totalAmount - $discountAmount);

        // Store in session with user ID to user later on.
        $time = now()->addMinutes(12);
        \Cache::put("promo_code_{$userId}", $promoCode->id, $time);
        \Cache::put("discount_amount_{$userId}", $discountAmount, $time);

        return response()->json([
            'success' => true,
            'discount' => $discountAmount,
            'new_total' => $newTotal,
            'message' => 'Promo code applied successfully!',
        ]);

//        $request->validate([
//            'user_id' => 'required|exists:users,id',
//            'total' => 'required|numeric',
//            'promo_code' => 'nullable|string',
//        ]);
//
//        $promoCode = PromoCode::where('code', $request->promo_code)->first();
//        $discountAmount = 0;
//
//        if ($promoCode && $promoCode->isValid()) {
//            $discountAmount = $promoCode->discount_amount ?? ($request->total * ($promoCode->discount_percentage / 100));
//            $promoCode->increment('used_count');
//        }
//
//        $order = Order::create([
//            'user_id' => $request->user_id,
//            'total' => $request->total - $discountAmount,
//            'promo_code_id' => $promoCode->id ?? null,
//            'discount_applied' => $discountAmount,
//        ]);

    }
}

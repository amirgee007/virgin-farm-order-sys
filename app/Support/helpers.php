<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Vanguard\Models\Box;
use Vanguard\Models\Carrier;
use Vanguard\Models\Cart;
use Vanguard\Models\ClientNotification;
use Vanguard\Models\ProductQuantity;
use Vanguard\Models\Setting;
use Vanguard\Models\ShippingAddress;
use Vanguard\Models\UsState;

function getMyCart()
{
    return Cart::mineCart()->get();
}

function cartTimeLeftSec()
{
    $user = itsMeUser();
    $cartUpdated = Cart::where('user_id', $user->id)->value('updated_at');

    $remainingSeconds = 0;

    if ($cartUpdated) {
        $lastAddedTime = new Carbon($cartUpdated);
        $currentTime = Carbon::now();
        $expirationTime = $lastAddedTime->copy()->addHour();

        $remainingSeconds = $currentTime->diffInSeconds($expirationTime, false);  // 'false' to allow negative values
    }

    return $remainingSeconds > 0 ? $remainingSeconds : 0;

}

function getNonUSCarrier()
{
    return [2, 3, 10, 25, 48];
}

function myPriceColumn()
{
    #OPTIMIZATION required plz keep in mind to reduce conditions
    $user = itsMeUser();
    $prices = getPrices();

    # if user is non-americal then use the price always price_fob
    if ($user->state > 52)
        return 'price_fob';

    $column = $user->price_list ? $prices[$user->price_list] : 'price_fedex';

    #if price is FEDEX and carrier  other than fedex then price fob
    if ($user->price_list == 1 && $user->carrier_id != 23)
        $column = 'price_fob';

    #if price is FOB and carrier FedEx then use fedex price
    elseif ($user->price_list == 2 && $user->carrier_id == 23)
        $column = 'price_fedex';

    #if price is Hawai and carrier other than FedEx then use fob
    elseif ($user->price_list == 3 && $user->carrier_id != 23)
        $column = 'price_fob';

    return $column;
}

function isDeliveryChargesApply()
{
    $user = itsMeUser();
    $note = null;

    #Carrier not PU(32), Not Federal Express(23) New LOGIC
    if (!in_array($user->carrier_id, [23, 32]))
        $note = 'Delivery charges may apply.';

    return $note;
}

function getCubeSizeTax($size)
{
    #PU(32), Federal Express(23) , DLV(17)
    $user = itsMeUser();

    $salesRepExtra = in_array($user->sales_rep, ['Robert', 'Mario', 'Joe']);

    $tax = $boxChargesApply = $serviceTransportFees24 = $additional = $extraTax = 0;

    #If carrier is not Federal Express then apply 24%
    if ($user->carrier_id != 23)
        $serviceTransportFees24 = true;

    #Carrier is fedex then apply BOX CHARGES ONLY one case
    if ($user->carrier_id == 23)
        $boxChargesApply = true;

    if ($boxChargesApply) {

        if ($size >= 12 && $size <= 16) {
            $tax = 32;
            $extraTax = 1;
        } elseif ($size >= 18 && $size <= 21) {
            $tax = 35;
            $extraTax = 1;
        } elseif ($size >= 22 && $size <= 25) {
            $tax = 33;
            $extraTax = 1;
        } elseif ($size >= 27 && $size <= 30) {
            $tax = 34.5;
            $extraTax = 1;
        } elseif ($size >= 40) {  // Combined the last two conditions
            $tax = 36;
            $extraTax = 2;
        }

        if ($salesRepExtra)
            $tax = $tax + $extraTax;

        if ($size / 45 > 1) {
            $countMore45 = ((int)ceil($size / 45) - 1);
            $additional = 33 * $countMore45;
        }
    }

    if ($serviceTransportFees24) #FOB fee charge formula
        $tax = $size * 0.24; #fixed 0.24 Example: 45 cubes * 0.24 = $10.80

    $total = round2Digit($additional + $tax);
    $extra = 0;

    try {

        #{"date_in":"2025-01-27","date_out":"2025-02-13"}
        #checked if week is during the PRICES then up it too.
        $found = Setting::where('key', 'extra-fees-date')->where('value', '>', 0)->first();
        if ($found) {
            $dates = json_decode($found->label, true);

            $start = Carbon::parse($dates['date_in']);
            $end = Carbon::parse($dates['date_out']);

            $user = itsMeUser();
            $date_shipped = Carbon::parse($user->last_ship_date);

            if ($date_shipped->between($start, $end)) {
                $extra = round2Digit(($found->value / 100) * $total);
            }
        }
    } catch (\Exception $ex) {
        Log::error($ex->getMessage() . ' error calcualting extra percentage data/values etc during transportation. ');
    }

    return $total + $extra;
}

function getCubeRangesV2($size)
{

    $size = $size > 220 ? 220 : $size;

    $boxCombination = null;
    $percentage = null;
    $total = 0;

    if (checkIfSkipCubeRangeCondition()) {
        $boxCombination = 'N/A';
        $percentage = 100;
        $total = 0;
    } else {
        $user = auth()->user();
        $stateNotAllow22 = false;

        if (in_array($user->state, [1, 12]))
            $stateNotAllow22 = true;

        #Restrict Hawaii and Alaska customers only, cannot purchase the medium box. Must be above 22 cubes (medium large boxes and up).
        if ($stateNotAllow22)
            $boxes = Box::where('min_value', '>', 22)->orderBy('id')->get();
        else
            $boxes = Box::orderBy('id')->get();

        // Loop through each box and check if the size falls within its range
        foreach ($boxes as $box) {
            if ($size >= $box->min_value && $size <= $box->max_value) {
                $boxCombination = $box->description;
                // Since the size falls within a box range, set the percentage to 100%
                $percentage = 100;
                $total = calculateTotalPackingBox($boxCombination);
                break;
            }
        }

        // If no box combination is found, calculate the next closest size and percentage filled
        if (!$boxCombination) {
            foreach ($boxes as $box) {
                if ($size < $box->min_value) {
                    $nextSize = $box->min_value;

                    // Calculate the percentage to the next size
                    $difference = $nextSize - $size;
                    $percentage = round((($size / $nextSize) * 100), 0);
                    break;
                }
            }
        }
    }

    return [
        'size' => $size,
        'boxMatched' => $boxCombination,
        'percentage' => $percentage,
        'countBoxes' => $total
    ];
}

function calculateTotalPackingBox($inputString)
{
    try {
        // Check if there's a comma in the string 1 S, 1 L, 1 ML
        if (strpos($inputString, ',') !== false) {
            // Split the input string by comma
            $parts = explode(',', $inputString);

            // Initialize the total sum
            $totalSum = 0;

            foreach ($parts as $part) {
                // Remove all letters and keep only numbers
                $numbers = preg_replace('/[A-Za-z\s]/', '', $part);

                // Sum the numbers
                $totalSum += intval($numbers);
            }

            return $totalSum > 0 ? $totalSum : 1;
        } else {
            // No comma found, remove all letters and keep only numbers 2 x ML
            $numbers = preg_replace('/[A-Za-z\s]/', '', $inputString);
            // Return the number found, or 1 if no number found
            return intval($numbers) > 0 ? intval($numbers) : 1;
        }

    } catch (\Exception $ex) {
        Log::error($ex->getMessage() . ' error calculateTotal function ' . $inputString);
        return 0;
    }
}

function checkIfSkipCubeRangeCondition()
{
    $user = itsMeUser();
    $response = false;

    #Yes, cube range requirement applies only to fedex carrier method.
    if ($user && ($user->carrier_id != 23 || $user->edit_order_id)) #because 1 is add-on new order
        $response = true;

    return $response;
}

function getPrices()
{
    #if any change plz check this too 1,2,3 getCubeSizeTax
    return [
        0 => 'Select Price',
        1 => 'price_fedex',
        2 => 'price_fob', #dont change this ID as its using somewhere.
        3 => 'price_hawaii',
    ];
}

function getCarriers($nonUS = false)
{
    $onlyThese = $nonUS ? getNonUSCarrier() : [];

    $carriers = Carrier::pluck('carrier_name', 'id')->sortBy('c_code')->toArray();

    // Check if the first array is empty
    if (empty($onlyThese)) {
        $filteredArray = $carriers; // Return all of the second array
    } else {
        // Filter the second array to include only matching keys from the first array
        $filteredArray = array_intersect_key($carriers, array_flip($onlyThese));
    }

    return $filteredArray;
}

function getStates()
{
    $states = UsState::orderby('state_name')
        ->pluck('state_name', 'id')
        ->toArray();

    $states = [null => 'Select US State'] + $states;

    return $states;
}

function round2Digit($number)
{
    $roundedValue = round(floatval($number), 2);
    return number_format($roundedValue, 2, '.', '');
}

function diff4Human($date)
{
    return is_null($date) ? 'n/a' : Carbon::parse($date)->diffForHumans();
}

function getTerms()
{
    return [
        'N1', 'CC', 'Check by Phone'
    ];
}

function myRoleName()
{
    return auth()->user() ? auth()->user()->role->name : '';
}

function itsMeUser()
{
    return \Vanguard\User::find(auth()->id());
}

function getSalesReps()
{
    #if any change plz check this too 1,2,3 getCubeSizeTax
    return [
        '0' => 'Select Sales Rep',
        'Esteban' => 'Esteban',
        'Joe' => 'Joe',
        'Mario' => 'Mario',
        'Nestor' => 'Nestor',
        'Peter' => 'Peter',
        'Robert' => 'Robert',
    ];
}

function getSalesRepsNameEmail($name)
{
    # Define the mapping of names to emails.
    $emails = [
        'Mario' => 'mariop@virginfarms.com',
        'Robert' => 'robertm@virginfarms.com',
        'Joe' => 'joep@virginfarms.com',
        'Nestor' => 'nestorn@virginfarms.com',
    ];

    # Return the email associated with the name, or a default email if the name is not found.
    return $emails[$name] ?? 'amirseersol@gmail.com';
}

function checkAvailableQty($product_id)
{

    $user = itsMeUser();
    $date_shipped = $user->last_ship_date;

    return ProductQuantity::where('product_quantities.product_id', $product_id)
        ->leftjoin('carts', 'carts.product_id', '=', 'product_quantities.id')
        ->whereRaw('"' . $date_shipped . '" between `date_in` and `date_out`')
        ->selectRaw('product_quantities.quantity-COALESCE(carts.quantity, 0) as quantity')
        ->first();
}

function cleanArray($array)
{
    $result = array();
    foreach ($array as $key => $value) {
        $key = strip_tags($key);
        if (is_array($value)) {
            $result[$key] = cleanArray($value);
        } else {
            $result[$key] = trim(strip_tags($value)); // Remove trim() if you want to.
        }
    }
    return $result;
}

function dateFormatMy($date)
{
    return Carbon::parse($date)->format('m/d/Y');
}

function dateFormatRecent($date)
{
    return Carbon::parse($date)->toFormattedDayDateString();
}

function dateRangeConverter($dateInOut)
{

    $date_range = explode("-", $dateInOut);

    $date_in = now()->toDateString();
    $date_out = now()->toDateString();

    if (!empty(array_filter($date_range))) {
        $date_in = Carbon::parse(trim($date_range[0]))->toDateString();
        $date_out = Carbon::parse(trim($date_range[1]))->toDateString();
    }

    return compact('date_in', 'date_out');
}

function getAddOnDetail($order)
{
    $text = '';
    try {
        if ($order->full_add_on == 1)
            $text = 'Add-On General';
        if ($order->full_add_on == 2)
            $text = 'Add-On #W-' . $order->id;

        #Add-On General is for people who have an order in our system (like a standing order)  and they want to add to that order but purchase online
        #What we do on our side is add it in our system to that ship date
    } catch (\Exception $ex) {
        Log::error('something went wrong during get add on detail ' . $ex->getMessage());
    }

    return $text;
}

function updateSystemStatus($value = 1)
{
    Setting::where('key', 'admin-uploading')->update(['value' => $value]);
}

#if user id is zero then it means its for ADMIN

function addOwnNotification($message, $order_id = 0, $user_id = 0)
{
    $data = [
        'message' => $message,
        'order_id' => $order_id,
        'user_id' => $user_id
    ];

    ClientNotification::updateOrcreate($data, $data);
}

function isReadFAQ()
{
    return auth()->user()->announcements_last_read_at;

//    auth()->user()->forceFill([
//        'announcements_last_read_at' => now()
//    ])->save();
}

function stripXSS($data)
{
    $sanitized = cleanArray($data);
    return $sanitized;
}


function getReportColumns()
{
    // Define the options as an associative array
    $columns = [
        "product_text" => "Product Description",
        "item_no" => "Item Number",
        "price_fob" => "Price FOB",
        "price_fedex" => "Price FedEx",
        "price_hawaii" => "Price Hawaii",
        "quantity" => "Available"
    ];

    return $columns;
}

function getContractCodes()
{
    return [
        1 => "FedEx",
        2 => "FOB",
        3 => "HI & AK"
    ];
}

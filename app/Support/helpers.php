<?php

use Carbon\Carbon;
use Vanguard\Models\Box;
use Vanguard\Models\Carrier;
use Vanguard\Models\Cart;
use Vanguard\Models\ProductQuantity;
use Vanguard\Models\Setting;
use Vanguard\Models\UsState;

function getMyCart()
{
    return Cart::mineCart()->get();
}
function myPriceColumn(){
    #OPTIMIZATION required plz keep in mind to reduce conditions
    $user = itsMeUser();
    $prices = getPrices();

    $column = $prices[$user->price_list];

    #if price is FEDEX and carrier  other than fedex then price fob
    if($user->price_list == 1 && $user->carrier_id != 23)
        $column = 'price_fob';

    #if price is FOB and carrier FedEx then use fedex price
    elseif($user->price_list == 2 && $user->carrier_id == 23)
        $column = 'price_fedex';

    #if price is Hawai and carrier other than FedEx then use fob
    elseif($user->price_list == 3 && $user->carrier_id != 23)
        $column = 'price_fob';

       return $column;
}

function isDeliveryChargesApply(){
    $user = itsMeUser();
    $note = null;

    #Carrier not PU(32), Not Federal Express(23) New LOGIC
    if(!in_array($user->carrier_id , [23,32]))
        $note = 'Delivery charges may apply.';

    return $note;
}

function getCubeSizeTax($size){

    #PU(32), Federal Express(23) , DLV(17)
    $user = itsMeUser();

    $salesRepExtra = in_array($user->sales_rep , ['Robert', 'Mario', 'Joe']);

    $tax = $boxChargesApply = $serviceTransportFees24 = $additional = $extraTax = 0;

    #If carrier is not Federal Express then apply 24%
    if($user->carrier_id != 23)
        $serviceTransportFees24 = true;

    #Carrier is fedex then apply BOX CHARGES ONLY one case
    if($user->carrier_id == 23)
        $boxChargesApply = true;

    if($boxChargesApply){
        if($size >= 12 && $size <= 16){
            $tax = 32;
            $extraTax = 1;
        }
        elseif($size >= 18 && $size <= 21) {
            $tax = 35;
            $extraTax = 1;
        }
        elseif($size >= 22 && $size <= 25) {
            $tax = 33;
            $extraTax = 1;
        }
        elseif($size >= 27 && $size <= 30) {
            $tax = 34.5;
            $extraTax = 1;
        }
        elseif($size >= 40 && $size <= 45) {
            $tax = 36;
            $extraTax = 2;
        }
        elseif($size > 45) {
            $tax = 36;
            $extraTax = 2;
        }

        if($salesRepExtra)
            $tax = $tax + $extraTax;

        if($size/45 > 1){
            $countMore45 = ((int)ceil($size/45) - 1);
            $additional = 33 * $countMore45;
        }
    }

    if($serviceTransportFees24)
        $tax = $size * 0.24; #fixed 0.24 Example: 45 cubes * 0.24 = $10.80

    $total =  round2Digit($additional + $tax);
    $extra = 0;

    try{
        #checked if week is during the PRICES then up it too.
        $found = Setting::where('key' , 'extra-fees-date')->where('value' , '>' , 0)->first();
        if($found){
            $dates = json_decode($found->label , true);

            $start = Carbon::parse($dates['date_in']);
            $end = Carbon::parse($dates['date_out']);

            $today = Carbon::today();

            if ($today->between($start, $end)) {
                $extra = round2Digit(($found->value / 100) * $total);
            }
        }
    }catch (\Exception $ex){
        Log::error($ex->getMessage() . ' error calcualting extra percentage data/values etc during transportation. ');
    }

    return $total+$extra;
}

function divideIntoGroupMax($number, $groupSize = 45) {
    $result = [];

    while ($number > 0) {
        $group = min($number, $groupSize);
        $result[] = $group;
        $number -= $group;
    }

    return $result;
}

function getCubeRanges($total)
{
    #if customer is Just for FOB when PU is carrier then no need to do the CUBE sizes
    $maxValue = 45;
    $max = Box::orderBy('max_value' , 'desc')->first();
    if($max && $max->max_value)
        $maxValue = $max->max_value;

    #logic is check if less than 45 then check the limit between all current boxes otherwise devide into 45, 45 and then check all.
    $values = $total > $maxValue ? divideIntoGroupMax($total , $maxValue) : [$total];
    $matched = [];

    foreach ($values as $value){
        $found = Box::where('min_value' , '<=' ,$value)->where('max_value' , '>=' , $value)->first();
        if($found)
            $matched[] = $found->description;
    }

    if(checkIfSkipCubeRangeCondition())
        return [$matched , true];

    return (count($values) == count($matched)) ? [$matched , true] : [$matched , false];
}

function checkIfSkipCubeRangeCondition()
{
    $user = itsMeUser();
    $response = false;

    #Yes, cube range requirement applies only to fedex carrier method.
    if($user->carrier_id != 23 || $user->edit_order_id) #because 1 is add-on new order
        $response = true;

    return  $response;
}

function getPrices(){
    #if any change plz check this too 1,2,3 getCubeSizeTax
    return [
        0 => 'Select Price',
        1 => 'price_fedex',
        2 => 'price_fob', #dont change this ID as its using somewhere.
        3 => 'price_hawaii',
    ];
}

function getCarriers(){
    return Carrier::pluck('carrier_name', 'id')->sortBy('c_code')->toArray();
}
function getStates(){

    $states = UsState::orderby('state_name')
        ->pluck('state_name', 'id')
        ->toArray();

    $states = [null => 'Select State'] + $states;

    return  $states;

    return [
        'Alabama',
        'Alaska',
        'Arizona',
        'Arkansas',
        'California',
        'Colorado',
        'Connecticut',
        'Delaware',
        'District of Columbia',
        'Florida',
        'Georgia',
        'Hawaii',
        'Idaho',
        'Illinois',
        'Indiana',
        'Iowa',
        'Kansas',
        'Kentucky',
        'Louisiana',
        'Maine',
        'Maryland',
        'Massachusetts',
        'Michigan',
        'Minnesota',
        'Mississippi',
        'Missouri',
        'Montana',
        'Nebraska',
        'Nevada',
        'New Hampshire',
        'New Jersey',
        'New Mexico',
        'New York',
        'North Carolina',
        'North Dakota',
        'Ohio',
        'Oklahoma',
        'Oregon',
        'Pennsylvania',
        'Rhode Island',
        'South Carolina',
        'South Dakota',
        'Tennessee',
        'Texas',
        'Utah',
        'Vermont',
        'Virginia',
        'Washington',
        'West Virginia',
        'Wisconsin',
        'Wyoming',
        'Puerto Rico',
        'U.S. Virgin Islands',
    ];
}

function round2Digit($number){
    $roundedValue = round(floatval($number), 2);
    return number_format($roundedValue, 2);
}
function diff4Human($date ){
    return is_null($date) ? 'n/a' : Carbon::parse($date)->diffForHumans();
}
function getTerms(){
    return  [
        'N1', 'CC', 'Check by Phone'
    ];
}
function myRoleName(){
    return  auth()->user() ? auth()->user()->role->name : '';
}
function itsMeUser(){
    return \Vanguard\User::find(auth()->id());
}

function getSalesReps(){
    #if any change plz check this too 1,2,3 getCubeSizeTax
    return [
        '0' => 'Select',
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

function checkAvailableQty($product_id){

    $user = itsMeUser();
    $date_shipped = $user->last_ship_date;

    return ProductQuantity::where('product_quantities.product_id' , $product_id)
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

function dateFormatMy($date){
    return Carbon::parse($date)->format('m/d/Y');
}

function dateFormatRecent($date){
    return Carbon::parse($date)->toFormattedDayDateString();
}

function dateRangeConverter($dateInOut){

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
    try{
        if($order->full_add_on == 1)
            $text = 'Add-On General';
        if($order->full_add_on == 2)
            $text = 'Add-On #W-'.$order->id;

    }catch (\Exception $ex){
        Log::error('something went wrong during get add on detail '.$ex->getMessage());
    }

    return $text;
}


function stripXSS($data)
{
    $sanitized = cleanArray($data);
    return $sanitized;
}

<?php

use Carbon\Carbon;
use Vanguard\Models\Box;
use Vanguard\Models\Carrier;
use Vanguard\Models\UsState;

function getMyCart()
{
    $carts = session()->get('cart', []);
    return $carts;
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

    #Price is FOB OR carrier not PU, not FEDEX  OR price is fedex and carrier is PU Or not fedex
    if(($user->price_list == 2 && ($user->carrier_id != 32 && $user->carrier_id != 23)) ||  ($user->price_list == 1  && ($user->carrier_id == 32 || $user->carrier_id != 23)))
        $note = 'Delivery charges may apply.';

    return $note;
}

function getCubeSizeTax($size){

    $user = itsMeUser();
    $priceName = $user->price_list; #1,2,3 fedex,fob,hawaii
    $salesRepExtra = in_array($user->sales_rep , ['Robert', 'Mario', 'Joe']);

    $tax = $boxChargesApply = $serviceTransportFees24 = $additional = $extraTax = 0;

    #All prices and carrier is fedex then apply BOX CHARGES
    if($user->carrier_id == 23)
        $boxChargesApply = true;

    #FOb and carrier is PU OR any other but not fedex then apply transport tax 24%
    if($user->price_list == 2  && ($user->carrier_id == 32 || $user->carrier_id != 23))
        $serviceTransportFees24 = true;

    #Fedex and carrier is PU and not fedex then apply transport tax 24%
    elseif($user->price_list == 1  && ($user->carrier_id == 32 || $user->carrier_id != 23))
        $serviceTransportFees24 = true;

    #hawai and carrier is not fedex then apply transport tax 24%
    elseif($user->price_list == 3  && $user->carrier_id != 23)
        $serviceTransportFees24 = true;

    if($boxChargesApply && $user->price_list != 1){ #only fedex, hawai have have box cube minumums now
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

//        if($salesRepExtra)
//            $additional = 33 * $countMore45;
//        else
//            $additional = 32 * $countMore45;
        }
    }

    if($serviceTransportFees24)
        $tax = $size * 0.24; #fixed 0.24 Example: 45 cubes * 0.24 = $10.80

    #return [$additional , $tax];
    return round2Digit($additional + $tax);
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
    if(checkIfSkipCubeRangeCondition())
        return true;

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

    return (count($values) == count($matched)) ? $matched : null;
}

function checkIfSkipCubeRangeCondition()
{
    $user = itsMeUser();
    $response = false;

    #FOb and carrier is PickUp, then no need  and apply for all except fedex
    if($user->price_list == 2 && ($user->carrier_id == 32 || $user->carrier_id != 23))
        $response = true;

    #Fedex and carrier is PickUp, then no need  and apply for all except fedex
    elseif($user->price_list == 1 && ($user->carrier_id == 32 || $user->carrier_id != 23))
        $response = true;

    #hawai and carrier except  fedex then no cube required
    elseif($user->price_list == 3 && ($user->carrier_id != 23))
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


function stripXSS($data)
{
    $sanitized = cleanArray($data);
    return $sanitized;
}

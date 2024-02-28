<?php

use Carbon\Carbon;
use Vanguard\Models\Box;
use Vanguard\Models\Carrier;
use Vanguard\Models\UsState;

function round2Digit($number){
    \Illuminate\Support\Facades\Log::info('round 2 digits before '.$number);
    $roundedValue = round(floatval($number), 2);
    \Illuminate\Support\Facades\Log::info('round 2 digits after '.$number);
    return number_format($roundedValue, 2);
}
function diff4Human($date ){
    return is_null($date) ? 'n/a' : Carbon::parse($date)->diffForHumans();
}

function myPriceColumn(){

    $user = auth()->user();
    $prices = getPrices();

    $column = $prices[$user->price_list];

    #if price is FOB and carrier FedEx then use fedex, otherwise FOB
    if($user->price_list == 2 && $user->carrier_id == 23)
        $column = 'price_fedex';

       return $column;
}

#For FOB Customers that choose Delivery (DLV) we need a note that states:
# Delivery charges may apply. At the order summary page and also on the copy of the order total emailed to them.
function isDeliveryChargesApply(){
    $user = auth()->user();
    $note = null;

    if($user->price_list == 2 && in_array($user->carrier_id , [17])) #PU
        $note = 'Delivery charges may apply';

    return $note;
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

function getSalesReps(){
    #if any change plz check this too 1,2,3 getCubeSizeTax
    return [
        '0' => 'Select',
        'Mario' => 'Mario',
        'Robert' => 'Robert',
        'Joe' => 'Joe',
        'Nestor' => 'Nestor',
        'Peter' => 'Peter',
    ];
}

function getCubeSizeTax($size){

//    if(checkIfSkipCubeCondition())
//        return 0.00;

    $priceName = auth()->user()->price_list; #1,2,3 fedex,fob,hawaii
    $salesRepExtra = in_array(auth()->user()->sales_rep , ['Robert', 'Mario', 'Joe']);

    $tax = $additional = $extraTax = 0;

    #only fedex have box cube minumums now
    #all other no limit but 0.24 apply and service trans fees may apply in email too
    if($priceName == 1){ #only fedex have box cube minumums now
        if($size >= 12 && $size <= 15){
            $tax = 32;
            $extraTax = 1;
        }
        elseif($size >= 16 && $size <= 20) {
            $tax = 35;
            $extraTax = 1;
        }
        elseif($size >= 21 && $size <= 25) {
            $tax = 33;
            $extraTax = 1;
        }
        elseif($size >= 28 && $size <= 31) {
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

    #if price is FOB then do this
    else{
        $tax = $size * 0.24; #fixed 0.24 Example: 45 cubes * 0.24 = $10.80
    }

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

function getCubeSize($total)
{
    if(checkIfSkipCubeCondition())
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

function checkIfSkipCubeCondition()
{
    ##Just for FOB price when carrier is PU then no need to check cube limits and no fees
    $user = auth()->user();
    return  ($user->price_list == 2 && $user->carrier_id == 32);
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


function getTerms(){
    return  [
        'N1', 'CC', 'Check by Phone'
    ];
}

function myRoleName(){
    return  auth()->user() ? auth()->user()->role->name : '';
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

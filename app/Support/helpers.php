<?php

use Carbon\Carbon;
use Vanguard\Models\Box;
use Vanguard\Models\Carrier;

function diff4Human($date ){
    return is_null($date) ? 'n/a' : Carbon::parse($date)->diffForHumans();
}

function getPrices(){
    #if any change plz check this too 1,2,3 getCubeSizeTax
    return [
        0 => 'Select Price',
        1 => 'price_fedex',
        2 => 'price_fob',
        3 => 'price_hawaii',
    ];
}

function getCubeSizeTax($size){

    $priceName = auth()->user()->price_list; #1,2,3 fedex,fob,hawaii

    $tax = $additional = 0;
    if($priceName == 2){
        $tax = $size * 0.24; #fixed 0.24 Example: 45 cubes * 0.24 = $10.80
    }
    #FedEx /HI&AK Fee Charges
    else{
        if($size <= 15)
            $tax = 31;
        elseif($size >= 16 && $size <= 20)
            $tax = 34;
        elseif($size >= 21 && $size <= 25)
            $tax = 32;
        elseif($size >= 28 && $size <= 31)
            $tax = 33;
        elseif($size >= 40 && $size <= 45)
            $tax = 34;
        elseif($size > 45)
            $tax = 34;
    }

    if($size/45 > 1){
        $additional = 32 * ((int)ceil($size/45) - 1);
    }

    return [$additional , $tax];
}

function getCubeSize($total)
{
    #check cube ranges if match then good otherwise check if its largest
    $found = Box::where('min_value' , '<=' ,$total)->where('max_value' , '>=' , $total)->first();
    if(is_null($found)){
        $max = Box::orderBy('max_value' , 'desc')->first();
        if($max && $max->max_value <= $total)
            $found = $max;
    }
    return $found;
}

function getCarriers(){
    return Carrier::pluck('c_code', 'id')->sortBy('c_code')->toArray();
}

function getStates(){
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

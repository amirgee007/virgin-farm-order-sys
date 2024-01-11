<?php

use Carbon\Carbon;
use Vanguard\Models\Carrier;

function diff4Human($date ){
    return is_null($date) ? 'n/a' : Carbon::parse($date)->diffForHumans();
}

function getPrices(){
    return [
        0 => 'Select Price',
        1 => 'Fedex',
        2 => 'FOB',
        3 => 'Hawaii',
    ];
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

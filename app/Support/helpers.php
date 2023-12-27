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


function stripXSS($data)
{
    $sanitized = cleanArray($data);
    return $sanitized;
}

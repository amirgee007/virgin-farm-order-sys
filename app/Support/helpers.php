<?php

use Carbon\Carbon;

function diff4Human($date ){
    return is_null($date) ? 'n/a' : Carbon::parse($date)->diffForHumans();
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


function stripXSS($data)
{
    $sanitized = cleanArray($data);
    return $sanitized;
}

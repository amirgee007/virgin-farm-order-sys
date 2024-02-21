<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;

class TestAmirController extends Controller
{

    public function index2($value = 0){

        $result = getCubeSize($value);

        dd($result);
        dd(implode(', ' ,$result));
        if ($result !== null) {
            echo "$valueToCheck is within the valid range.";
        } else {
            echo "$valueToCheck is not within the valid range.";
        }

    }
}

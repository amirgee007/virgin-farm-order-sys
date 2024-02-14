<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;

class TestAmirController extends Controller
{

    public function index2($value = 0){

        $rand = $value ? $value : rand(12 , 500);

        $ok = getCubeSizeTax($rand);

        $additional = 12.344;
        $tax = 12.344;

        dd(round($additional + $tax , 2));

        dd($ok);

        echo 'Estimated Fees for CUBE: '.$rand;

        echo '</br> ';

        echo 'Service/Transportation: '.$ok;

    }
}

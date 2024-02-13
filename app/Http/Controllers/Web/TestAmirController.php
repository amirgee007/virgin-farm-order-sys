<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;

class TestAmirController extends Controller
{

    public function index2($value = 0){

        $rand = $value ? $value : rand(12 , 500);

        $ok = getCubeSizeTax($rand);

        echo 'Estimated Fees for CUBE: '.$rand;

        echo '</br> ';

        echo 'Additional Fees: '.$ok[0];

        echo '</br> ';

        echo 'Tax: '.$ok[1];

    }
}

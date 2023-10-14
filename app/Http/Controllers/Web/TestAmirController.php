<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Http\Request;
use Vanguard\Http\Controllers\Controller;

class TestAmirController extends Controller
{

    public function index2(){

        dd(now()->toDateTimeString());
    }
}

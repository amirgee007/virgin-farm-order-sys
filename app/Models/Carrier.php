<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;

    protected $table = 'carriers';
    protected $guarded = [];

    #FedEx Ecuador  and  Pick Up
    public static $farmsDirectIds = [
        20, 32
    ];

    #FedEx Ecuador  Hide from all except farmsDirect
    public static $hideCarriersExceptFarmsDirect = [
        20
    ];
}

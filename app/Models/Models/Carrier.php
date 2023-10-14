<?php

namespace Vanguard\Models\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    use HasFactory;

    protected $table = 'carriers';
    protected $guarded = [];
}

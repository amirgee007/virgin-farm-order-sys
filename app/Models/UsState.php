<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsState extends Model
{
    use HasFactory;

    protected $table = 'us_states';
    protected $guarded = [];
}

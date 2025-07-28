<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartSnapshot extends Model
{
    use HasFactory;

    protected $table = 'cart_snapshots';
    protected $guarded = [];
}

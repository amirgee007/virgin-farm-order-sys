<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQuantity extends Model
{
    use HasFactory;
    protected $table = 'product_quantities';
    protected $guarded = [];
}

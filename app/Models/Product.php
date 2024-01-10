<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $guarded = [];

    public function prodQty() {
        return $this->hasMany(ProductQuantity::class , 'product_id' , 'product_id');
    }

}

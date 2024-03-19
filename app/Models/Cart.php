<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'carts';
    protected $guarded = [];

    public function scopeMineCart($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function product() {
        return $this->hasOne(ProductQuantity::class , 'id' , 'product_id');
    }
}

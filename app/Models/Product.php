<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vanguard\Models\ProductGroup;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $guarded = [];

    public function prodQty() {
        return $this->hasMany(ProductQuantity::class , 'product_id' , 'id');
    }

    public function stemsCount() {
        return $this->hasOne(UnitOfMeasure::class , 'unit' , 'unit_of_measure');
    }

    public function groups()
    {
        return $this->belongsToMany(ProductGroup::class, 'product_group_product')
            ->withPivot('stems');
    }

    public function groupProducts()
    {
        return $this->belongsToMany(Product::class, 'product_group_product', 'product_group_id', 'product_id')
            ->withPivot('stems');
    }

}

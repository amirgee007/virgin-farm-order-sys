<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name' , 'parent_product_id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_group_product')
            ->withPivot('stems');
    }

    public function parentProduct() {
        return $this->hasOne(Product::class , 'id' , 'parent_product_id');
    }
}


<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishListItem extends Model
{
    use HasFactory;

    protected $table = 'wish_list_items';
    protected $guarded = [];

    public function wishList()
    {
        return $this->belongsTo(WishList::class, 'wish_list_id', 'id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}

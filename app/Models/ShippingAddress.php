<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vanguard\User;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $table = 'shipping_addresses';
    protected $guarded = [];


//    public function scopeAvailable($query){
//        return $query->where('is_used', 0);
//    }

    public function user() {
        return $this->hasOne(User::class , 'id' , 'user_id');
    }
}

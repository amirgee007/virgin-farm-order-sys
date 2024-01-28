<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vanguard\User;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $guarded = [];

    public function items() {
        return $this->hasMany(OrderItem::class , 'order_id' , 'id');
    }

    public function getShipToAttribute()
    {
        return $this->company.' '. $this->shipping_address;
    }

    public function user() {
        return $this->hasOne(User::class , 'id' , 'user_id');
    }

    public function carrier()
    {
        return $this->hasOne(Carrier::class , 'id' , 'carrier_id');
    }
}

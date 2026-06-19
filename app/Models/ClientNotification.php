<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vanguard\User;

class ClientNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $guarded = [];

    public function user() {
        return $this->hasOne(User::class , 'id' , 'user_id');
    }

    public function wishList() {
        return $this->belongsTo(WishList::class, 'wish_list_id', 'id');
    }

    public function order() {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function scopeMine($query){
        $user_id = myRoleName() == 'Admin' ? 0 : auth()->id();
        return $query->where('user_id' , $user_id);
    }

    public function scopeUnread($query){
        return $query->whereNull('read_at');
    }
}

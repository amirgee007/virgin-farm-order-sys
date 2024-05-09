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

    public function scopeMine($query){
        $user_id = myRoleName() == 'Admin' ? 0 : auth()->id();
        return $query->where('user_id' , $user_id);
    }
}

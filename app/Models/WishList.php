<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Vanguard\User;

class WishList extends Model
{
    use HasFactory;

    protected $table = 'wish_lists';
    protected $guarded = [];
    protected $casts = [
        'request_date' => 'date',
        'submitted_at' => 'datetime',
    ];

    public function scopeMineDraft($query)
    {
        return $query->where('user_id', auth()->id())->where('status', 'draft');
    }

    public function scopeMine($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function items()
    {
        return $this->hasMany(WishListItem::class, 'wish_list_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function countQty()
    {
        return $this->items()->sum('quantity');
    }
}

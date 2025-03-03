<?php

namespace Vanguard\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'discount_amount', 'discount_percentage', 'max_usage', 'used_count',
        'valid_from', 'valid_until', 'is_active', 'promo_disc_class'
    ];

    public function isValid()
    {
        return $this->is_active
            && ($this->valid_from == null || now() >= $this->valid_from)
            && ($this->valid_until == null || now() <= $this->valid_until)
            && ($this->max_usage == null || $this->used_count < $this->max_usage);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_code',
        'discount_type',
        'discount_amount',
        'expiry_date',
        'usage_type',
        'usage_limit',
        'description',
        'status'
    ];
}

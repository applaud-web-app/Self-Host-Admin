<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'amount',
        'coupon_code',
        'discount_amount',
        'support_years',
        'support_yearly_price',
        'metadata',
        'is_grouped',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function license()
    {
        // assumes your licenses table has a payment_id FK
        return $this->hasOne(License::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'payment_id',
        'raw_key',
        'key_salt',
        'key_hash',
        'activated_domain',
        'activated_ip',
        'is_activated',
        'status',
        'issued_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}

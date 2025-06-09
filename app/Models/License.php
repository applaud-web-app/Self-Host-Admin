<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'payment_id',
        'key',
        'status',
        'issued_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

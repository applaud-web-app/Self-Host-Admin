<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    protected $fillable = [
        'uuid',
        'slug',
        'name',
        'icon',
        'version',
        'price',
        'type',
        'description',
        'status'
    ];

    public function payment()
    {
        return $this->hasOne(Payment::class)
                    ->where('user_id', Auth::id())
                    ->select('id', 'product_id', 'status');
    }

    public function license()
    {
        return $this->hasOne(License::class)
                    ->where('user_id', Auth::id())
                    ->select('product_id', 'key');
    }
}

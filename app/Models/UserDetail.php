<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';
    protected $fillable = [
        'user_id',
        'billing_name',
        'state',
        'city',
        'pin_code',
        'address',
        'pan_card',
        'gst_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

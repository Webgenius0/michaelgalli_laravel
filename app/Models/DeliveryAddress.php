<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{
    protected $fillable = [
        'user_id',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'landmark',
        'is_default',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    protected $table = 'subscription_features';

    protected $fillable = [
        'description',
        'include_description',
    ];


}

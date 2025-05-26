<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealPlan extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'name',
        'people',
        'recipes_per_week',
        'price_per_serving',
        'stripe_price_id',
    ];

    
    
}

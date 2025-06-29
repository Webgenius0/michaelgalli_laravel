<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlanCart extends Model
{
    protected $fillable = [
        'user_id',
        'meal_plan_id',
        'meal_plan_option_id',
        'price_per_serving',
        'people_count',
        'recipes_per_week',
        'total_servings',
        'total_price',
        'status',
    ];
}

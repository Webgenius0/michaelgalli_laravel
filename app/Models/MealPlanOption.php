<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlanOption extends Model
{
    protected $table = 'meal_plan_options';

    protected $fillable = [
        'meal_plan_id',
        'price_per_serving',
        'recipes_per_week',
        'is_recommanded'
    ];

    protected $casts = [
        'is_recommanded'  => 'boolean'
    ];



    public function meal_plan()
    {
        return $this->belongsTo(MealPlan::class);
    }
}

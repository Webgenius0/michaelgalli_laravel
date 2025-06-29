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
    ];



    public function meal_plan()
    {
        return $this->belongsTo(MealPlan::class);
    }
}

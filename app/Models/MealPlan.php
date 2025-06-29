<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlan extends Model
{

    protected $fillable = [
        'name',
        'people_count',

    ];

    public function options()
    {
        return $this->hasMany(MealPlanOption::class, 'meal_plan_id', 'id');
    }

}

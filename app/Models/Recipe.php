<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $guarded = ['id'];


    // imagegeturl


    public function ingredientSections()
    {
        return $this->hasMany(IngredientSection::class);
    }


    // instructions


    public function category()
    {
        return $this->hasMany(category::class);
    }

    public function instructions()
    {
        return $this->hasMany(RecipeInstruction::class);
    }


    public function calory()
    {
        return $this->belongsTo(Calories::class, 'calories_id', 'id');
    }


    public function protein()
    {
        return $this->belongsTo(Protein::class, 'protein_id', 'id');
    }


    public function carb()
    {
        return $this->belongsTo(Carb::class, 'carb_id', 'id');
    }

    public function cuisine()
    {
        return $this->belongsTo(Cuisine::class, 'cuisine_id', 'id');
    }

    public function health_goal()
    {
        return $this->belongsTo(HealthGoal::class, 'health_goal_id', 'id');
    }

    public function time_to_clock()
    {
        return $this->belongsTo(TimeToClock::class, 'time_to_clock_id', 'id');
    }
}

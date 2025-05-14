<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $guarded = ['id'];


    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'recipe_ingredients')
            ->withPivot('quantity', 'unit')  
            ->withTimestamps();
    }


    // instructions 

    public function instructions(){
        return $this->hasMany(RecipeInstruction::class);
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $guarded = ['id'];


    public function ingredientSections()
    {
        return $this->hasMany(IngredientSection::class);
    }


    // instructions 

    public function instructions(){
        return $this->hasMany(RecipeInstruction::class);
    }

}

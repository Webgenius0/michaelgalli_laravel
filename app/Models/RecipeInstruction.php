<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeInstruction extends Model
{
        protected $guarded = ['id'];


    // imagegeturl

    public function receipe(){
        return $this->belongsTo(Recipe::class);
    }
}

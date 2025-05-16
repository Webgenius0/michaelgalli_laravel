<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngredientSection extends Model
{
    protected $guarded = ['id'];

    protected $fillable = ['recipe_id', 'title', 'order'];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}

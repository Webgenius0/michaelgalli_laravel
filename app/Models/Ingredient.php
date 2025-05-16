<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['ingredient_section_id', 'name', 'amount', 'is_highlighted'];

    public function section()
    {
        return $this->belongsTo(IngredientSection::class, 'ingredient_section_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyRecipe extends Model
{
    protected $fillable = [
        'recipe_id',
        'week_start',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function scopeForWeek($query, $date)
    {
        return $query->where('week_start', $date);
    }
}

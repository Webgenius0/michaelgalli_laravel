<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderRecipe extends Model
{
    protected $table = 'order_recipes';

    protected $fillable = [
        'order_id',
        'recipe_id',
        'quantity',
        'price',
        'status', // pending, completed, cancelled
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}

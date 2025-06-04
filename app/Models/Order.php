<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'week_start',
        'status', // pending, completed, cancelled
    ];

    protected $casts = [
        'week_start' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recipes()
    {
        return $this->hasMany(OrderRecipe::class, 'order_id');
    }


    public function order_ingredients()
    {
        return $this->hasMany(OrderIngredient::class, 'order_id');
    }
}

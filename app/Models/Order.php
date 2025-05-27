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
        return $this->belongsToMany(Recipe::class, 'order_recipes')
                    ->withPivot('quantity', 'price', 'status')
                    ->withTimestamps();
    }
}

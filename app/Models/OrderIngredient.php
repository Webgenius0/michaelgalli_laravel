<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderIngredient extends Model
{
    protected $fillable = [
        'order_id',
        'recipe_id',
        'user_family_member_id',
        'original_ingredient',
        'swapped_ingredient',
        'reason'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function userFamilyMember()
    {
        return $this->belongsTo(UserFamilyMember::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFamilyCart extends Model
{
    protected $fillable = [
        'user_id',
        'user_family_member_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function user_family()
    {
        return $this->belongsTo(UserFamilyMember::class);
    }



}

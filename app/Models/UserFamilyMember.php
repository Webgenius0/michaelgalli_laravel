<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFamilyMember extends Model
{
    
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'relation',
        'age',
        'weight',
        'height'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    public function getAgeAttribute($value)
    {
        return $value . ' years';
    }
    public function getWeightAttribute($value)
    {
        return $value . ' kg';
    }

    public function getHeightAttribute($value)
    {
        return $value . ' cm';
    }


    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'user_family_member_id');
    }


    // question relationship
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'user_answers', 'user_family_member_id', 'question_id')
                    ->withPivot('answer_text', 'selected_option_value')
                    ->withTimestamps();
    }


    
}

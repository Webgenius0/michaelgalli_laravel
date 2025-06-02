<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionFamilyMember extends Model
{
    protected $table = 'subscription_family_members';



    protected $fillable = [
        'subscription_id',
        'user_family_member_id'        
    ];
}

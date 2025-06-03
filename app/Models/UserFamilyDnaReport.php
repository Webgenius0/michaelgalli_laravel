<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFamilyDnaReport extends Model
{
    protected $fillable = [
        'id',
        'user_family_member_id',
        'file_path',
        'report_data'
    ];
}

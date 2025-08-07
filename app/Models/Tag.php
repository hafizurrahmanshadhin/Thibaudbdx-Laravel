<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = [];
    protected $hidden = ['user_id', 'deleted_at'];
    
    protected $casts = [
        'user_id' => 'integer',
        'name' => 'string',
        'color' => 'string',
        'status' => 'string',

        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}

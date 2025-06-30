<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    protected $fillable = ['question', 'answer', 'type', 'status'];

    protected $casts = [
        'question' => 'string',
        'answer' => 'string',
        'type' => 'string',
        'status' => 'string',
    ];

    protected $table = 'f_a_q_s';
}

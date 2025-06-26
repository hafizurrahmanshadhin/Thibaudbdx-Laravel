<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at',  'customer_id', 'deleted_at', 'status','user_id'];

    protected $casts = [
        'date' => 'date',
        'reminder' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

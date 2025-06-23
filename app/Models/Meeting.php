<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'customer', 'customer_id', 'deleted_at', 'status'];

    protected $casts = [
        'date' => 'date',
        'reminder' => 'boolean',
    ];

    // Meeting.php
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}

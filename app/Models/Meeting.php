<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{

    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at',  'customer_id', 'deleted_at', 'status', 'user_id','customer'];

    protected $casts = [
        'customer_id'    => 'integer',
        'user_id'        => 'integer',
        'name'           => 'string',
        'description'    => 'string',
        'location'       => 'string',
        'date'           => 'date',
        'time' => 'string',
        'reminder'       => 'boolean',
        'reminder_time'  => 'integer',
        'status'         => 'string',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
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

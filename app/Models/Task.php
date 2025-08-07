<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at', 'status', 'customer_id','customer', 'user_id'];



    public function customer()
    {
        return $this->belongsTo(Customer::class,);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $casts = [
        'name'         => 'string',
        'customer_id'  => 'integer',
        'user_id'      => 'integer',
        'date'         => 'date',
        'time' => 'string',
        'description'  => 'string',
        'status'       => 'string',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];
}

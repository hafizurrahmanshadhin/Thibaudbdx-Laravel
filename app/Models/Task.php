<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{

    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at', 'status', 'customer_id', 'user_id'];

    protected $casts = [
        'date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class,);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = [];

    //hidden 
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'customer_id',
        'user_id'
    ];
    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class,);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasting extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at', 'product_id', 'customer', 'customer_id', 'user_id'];

    protected $casts = [
        'customer_id'  => 'integer',
        'user_id'      => 'integer',
        'name'         => 'string',
        'date'         => 'date',
        'product_id'   => 'array',
        'description'  => 'string',
        'type'         => 'string',
        'status'       => 'string',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    // Tasting.php
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

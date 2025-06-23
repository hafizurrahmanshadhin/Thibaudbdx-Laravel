<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tasting extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at', 'product_id', 'customer', 'customer_id'];

    protected $casts = [
        'product_id' => 'array',
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
}

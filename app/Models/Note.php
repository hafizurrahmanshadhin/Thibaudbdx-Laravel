<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Note extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at', 'created_at', 'user_id', 'status', 'customer_id'];


    protected $casts = [
        'customer_id' => 'integer',
        'user_id'     => 'integer',
        'date'        => 'date',
        'description' => 'string',
        'status'      => 'string',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docs extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at', 'status', 'user_id', 'customer_id'];



    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //live link 
    public function getFileAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }
        // If already a valid URL, just return it
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        // For API request: return full URL
        if (request()->is('api/*')) {
            return url($value);
        }
        // For web or other non-api: return relative path
        return $value;
    }
}

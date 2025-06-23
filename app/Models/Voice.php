<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at','customer','customer_id'];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    //voice live url response 
    public function getVoiceFileAttribute($value): ?string
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }

        return $value;
    }
}

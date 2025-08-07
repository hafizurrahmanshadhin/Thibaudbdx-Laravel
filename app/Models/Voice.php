<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voice extends Model
{
    protected $guarded = [];
    protected $hidden = ['updated_at', 'deleted_at', 'customer', 'customer_id', 'user_id'];

    protected $casts = [
        'customer_id'  => 'integer',
        'user_id'      => 'integer',
        'title'        => 'string',
        'description'  => 'string',
        'voice_file'   => 'string',
        'duration'     => 'integer',
        'date'         => 'date',
        'status'       => 'string',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

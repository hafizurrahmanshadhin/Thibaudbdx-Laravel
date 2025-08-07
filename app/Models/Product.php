<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];
    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'user_id'];

    public function getImageAttribute($value): string|null
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        // Check if the request is an API request
        if (request()->is('api/*') && !empty($value)) {
            // Return the full URL for API requests
            return url($value);
        }
        // Return only the path for web requests
        return $value;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'user_id' => 'integer',
        'wine_name' => 'string',
        'cuvee' => 'string',
        'type' => 'string',
        'color' => 'string',
        'soil_type' => 'string',
        'harvest_ageing' => 'string',
        'food' => 'string',
        'tasting_notes' => 'string',
        'awards' => 'string',
        'image' => 'string',
        'company_name' => 'string',
        'address' => 'string',
        'phone' => 'string',
        'email' => 'string',
        'website' => 'string',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}

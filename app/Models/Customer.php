<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'contact_type',
        'company_name',
        'owner_name',
        'address',
        'city',
        'zip_code',
        'phone',
        'email',
        'website',
        'tag_id',
        'description',
        'longitude',
        'latitude',
        'status'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'contact_type' => 'string',
        'company_name' => 'string',
        'owner_name' => 'string',
        'address' => 'string',
        'city' => 'string',
        'zip_code' => 'string',
        'phone' => 'string',
        'email' => 'string',
        'website' => 'string',
        'description' => 'string',
        'tag_id' => 'array',
        'longitude' => 'float',
        'latitude' => 'float',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];


    //hidden columns
    protected $hidden = ['user_id', 'deleted_at', 'tag_id', 'created_at', 'updated_at', 'status'];

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

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    function tastings()
    {
        return $this->hasMany(Tasting::class);
    }

    public function voices()
    {
        return $this->hasMany(Voice::class);
    }
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}

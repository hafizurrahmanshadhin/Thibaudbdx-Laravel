<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $guarded = [];
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }
}

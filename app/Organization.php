<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
class Organization extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password','bio','slogan','phone','location'
    ];

    /**
     * Get list of volunteers subscribed to an organization.
     */
    public function subscribers()
    {
      return $this->belongsToMany("App\User",
        "volunteers_subscribe_organizations")->withTimestamps();

    }

    public function recommendations()
    {
        $this->hasMany('App\Recommendation');
    }

}

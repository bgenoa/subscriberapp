<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    /**
     * Get the fields for the subscriber
     */
    public function fields()
    {
        return $this->hasMany('App\Field');
    }
}

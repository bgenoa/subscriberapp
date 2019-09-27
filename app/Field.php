<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    /**
     * Get the subscriber that owns the field.
     */
    public function subscriber()
    {
        return $this->belongsTo('App\Subscriber');
    }
}

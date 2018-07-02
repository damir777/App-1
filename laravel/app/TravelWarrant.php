<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TravelWarrant extends Model
{
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo('App\Employee');
    }

    public function creator()
    {
        return $this->belongsTo('App\Employee');
    }

    public function vehicle()
    {
        return $this->belongsTo('App\Vehicle');
    }
}

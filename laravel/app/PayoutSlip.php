<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayoutSlip extends Model
{
    public $timestamps = false;

    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }
}

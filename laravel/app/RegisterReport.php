<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class RegisterReport extends Model
{
    public $timestamps = false;

    public function office()
    {
        return $this->belongsTo('App\Office');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OfferProduct extends Model
{
    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}

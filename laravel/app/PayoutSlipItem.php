<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PayoutSlipItem extends Model
{
    public $timestamps = false;

    //form validation - items
    public static $items = [
        'item' => 'required',
        'sum' => 'required|decimal'
    ];
}

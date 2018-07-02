<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    public $timestamps = false;

    //form validation - tax percentage
    public static $tax_percentage = [
        'tax' => 'required|decimal'
    ];

    //form validation - tax date
    public static $tax_date = [
        'date' => 'required|custom_date'
    ];
}

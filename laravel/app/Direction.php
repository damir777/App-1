<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    public $timestamps = false;

    //form validation
    public static function validateDirectionForm()
    {
        $rules = [
            'date' => 'required|custom_date',
            'transport_type' => 'required',
            'start_location' => 'required',
            'end_location' => 'required',
            'distance' => 'required|integer',
            'km_price' => 'required|decimal'
        ];

        return $rules;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    public $timestamps = false;

    //form validation
    public static function validateCostForm()
    {
        $rules = [
            'date' => 'required|custom_date',
            'cost_type' => 'required',
            'description' => 'required',
            'sum' => 'required|decimal',
            'non_costs' => 'nullable|decimal'
        ];

        return $rules;
    }
}

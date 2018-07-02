<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Vehicle extends Model
{
    public $timestamps = false;

    //form validation - insert/update vehicle
    public static function validateVehicleForm($company_id = false, $id = false)
    {
        $rules = [
            'vehicle_type' => 'required',
            'name' => 'required',
            'register_number' => 'required',
            'year' => 'required|digits:4',
            'km' => 'required|integer'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('vehicles', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }

        return $rules;
    }
}

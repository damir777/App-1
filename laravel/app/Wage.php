<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Wage extends Model
{
    public $timestamps = false;

    public function wageCountry()
    {
        return $this->belongsTo('App\Country', 'country', 'code');
    }

    //form validation - insert/update wage
    public static function validateWageForm($company_id = false, $id = false)
    {
        $rules = [
            'name' => 'required',
            'price' => 'required|decimal',
            'country' => 'required|exists:countries,code'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('wages', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }

        return $rules;
    }
}

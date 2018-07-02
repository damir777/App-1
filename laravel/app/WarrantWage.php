<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class WarrantWage extends Model
{
    public $timestamps = false;

    public function wageWage()
    {
        return $this->belongsTo('App\Wage', 'wage_id');
    }

    public function wageCountry()
    {
        return $this->belongsTo('App\Country', 'country', 'code');
    }

    //form validation
    public static function validateWageForm($company_id)
    {
        $rules = [
            'country' => 'required|exists:countries,code',
            'date' => 'required|custom_date',
            'wage' => 'required|integer',
            'wage_type' => ['required', 'integer', Rule::exists('wages', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'start_time' => 'required|date_time',
            'end_time' => 'required|date_time'
        ];

        return $rules;
    }
}

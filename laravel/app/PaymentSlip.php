<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class PaymentSlip extends Model
{
    public $timestamps = false;

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    //form validation
    public static function validateSlipForm($company_id, $office_id)
    {
        $rules = [
            'payer' => 'required',
            'item' => 'required',
            'sum' => 'required|decimal'
        ];

        if ($office_id)
        {
            $rules['office'] = ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }
        else
        {
            $rules['office'] = 'required|integer';
        }

        return $rules;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Register extends Model
{
    public $timestamps = false;

    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    //form validation - insert/update register
    public static function validateRegisterForm($company_id, $id = false)
    {
        $rules = [
            'office' => ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })]
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('registers', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
            $rules['label'] = ['required', 'integer', Rule::unique('registers')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id);
            })->ignore($id)];

        }
        else
        {
            $rules['label'] = ['required', 'integer', Rule::unique('registers')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id);
            })];
        }

        return $rules;
    }
}

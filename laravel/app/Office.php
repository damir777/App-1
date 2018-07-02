<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Office extends Model
{
    public $timestamps = false;

    //form validation - insert/update office
    public static function validateOfficeForm($company_id, $id = false)
    {
        $rules = [
            'name' => 'required',
            'address' => 'required',
            'city' => 'required'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
            $rules['label'] = ['required', Rule::unique('offices')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id);
            })->ignore($id)];
        }
        else
        {
            $rules['label'] = ['required', Rule::unique('offices')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id);
            })];
        }

        return $rules;
    }
}

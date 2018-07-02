<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Employee extends Model
{
    public $timestamps = false;

    //form validation - insert/update employee
    public static function validateEmployeeForm($company_id = false, $id = false)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'job_title' => 'required'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('employees', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }

        return $rules;
    }
}

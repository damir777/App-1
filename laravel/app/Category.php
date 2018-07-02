<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Category extends Model
{
    public $timestamps = false;

    //form validation - insert/update category
    public static function validateCategoryForm($company_id = false, $id = false)
    {
        $rules = [
            'name' => 'required'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('categories', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }

        return $rules;
    }
}

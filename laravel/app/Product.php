<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Product extends Model
{
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo('App\Category');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit');
    }

    //form validation - insert/update product
    public static function validateProductForm($company_id, $id = false)
    {
        $rules = [
            'category' => ['required', 'integer', Rule::exists('categories', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'unit' => 'required|integer|exists:units,id',
            'tax_group' => ['required', 'integer', Rule::exists('tax_groups', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'name' => 'required',
            'price' => 'required|decimal',
            'service' => 'required|in:T,F'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('products', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
            $rules['code'] = ['required', Rule::unique('products')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id);
            })->ignore($id)];
        }
        else
        {
            $rules['code'] = ['required', Rule::unique('products')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id);
            })];
        }

        return $rules;
    }
}

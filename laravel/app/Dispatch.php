<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Dispatch extends Model
{
    public $timestamps = false;

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    //form validation - insert/update dispatch
    public static function validateDispatchForm($company_id, $id = false)
    {
        $rules = [
            'client' => ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'show_prices' => 'required|in:T,F',
            'products' => 'required|array',
            'products.*.id' => ['required', 'integer', Rule::exists('products', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'products.*.quantity' => 'required|decimal',
            'products.*.price' => 'required|decimal'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('dispatches', 'id')
                ->where(function($query) use ($company_id) {
                    $query->where('company_id', '=', $company_id); })];
            $rules['date'] = 'required|date_time';
            $rules['products.*.dp_id'] = ['nullable', 'integer', Rule::exists('dispatch_products', 'id')
                ->where(function($query) use ($id) {
                    $query->where('dispatch_id', '=', $id); })];
        }

        return $rules;
    }
}

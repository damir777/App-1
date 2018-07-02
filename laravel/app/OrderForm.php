<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class OrderForm extends Model
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

    //form validation - insert/update order form
    public static function validateOrderFormForm($company_id, $id = false)
    {
        $rules = [
            'client' => ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'delivery_date' => 'required|custom_date',
            'location' => 'required',
            'products' => 'required|array',
            'products.*.id' => ['required', 'integer', Rule::exists('products', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'products.*.quantity' => 'required|decimal',
            'products.*.price' => 'required|decimal'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('order_forms', 'id')
                ->where(function($query) use ($company_id) {
                    $query->where('company_id', '=', $company_id); })];
            $rules['date'] = 'required|date_time';
            $rules['products.*.ofp_id'] = ['nullable', 'integer', Rule::exists('order_form_products', 'id')
                ->where(function($query) use ($id) {
                    $query->where('order_form_id', '=', $id); })];
        }

        return $rules;
    }
}

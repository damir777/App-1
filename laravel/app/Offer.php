<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Offer extends Model
{
    public $timestamps = false;

    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    public function paymentType()
    {
        return $this->belongsTo('App\PaymentType');
    }

    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    //form validation - insert/update offer
    public static function validateOfferForm($company_id, $id = false, $create_invoice = false)
    {
        $rules = [
            'client' => ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'language' => 'required|integer|exists:languages,id',
            'payment_type' => 'required|integer|exists:payment_types,id',
            'currency' => 'required|integer|exists:currencies,id',
            'input_currency' => 'required|integer|exists:currencies,id',
            'valid_date' => 'required|custom_date',
            'tax' => 'required|in:T,F',
            'products' => 'required|array',
            'products.*.id' => ['required', 'integer', Rule::exists('products', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'products.*.quantity' => 'required|decimal',
            'products.*.price' => 'required|decimal',
            'products.*.custom_price' => 'required|in:T,F',
            'products.*.brutto' => 'required|in:T,F',
            'products.*.rebate' => 'required|decimal'
        ];

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('offers', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id); })];
            $rules['date'] = 'required|date_time';
            $rules['products.*.op_id'] = ['nullable', 'integer', Rule::exists('offer_products', 'id')
                ->where(function($query) use ($id) {
                    $query->where('offer_id', '=', $id); })];
            $rules['create_invoice'] = 'required|in:T,F';

            if ($create_invoice == 'T')
            {
                $rules['register'] = ['required', 'integer', Rule::exists('registers', 'id')
                    ->where(function($query) use ($company_id) {
                        $query->where('company_id', '=', $company_id); })];
                $rules['due_date'] = 'required|custom_date';
            }

            $rules['merchandise'] = 'required|in:T,F';
        }
        else
        {
            $rules['office'] = ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
        }

        return $rules;
    }
}

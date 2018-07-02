<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Invoice extends Model
{
    public $timestamps = false;

    public function office()
    {
        return $this->belongsTo('App\Office');
    }

    public function register()
    {
        return $this->belongsTo('App\Register');
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

    //form validation - insert/update invoice
    public static function validateInvoiceForm($company_id, $retail, $id = false, $status = false)
    {
        $rules = [
            'language' => 'required|integer|exists:languages,id',
            'payment_type' => 'required|integer|exists:payment_types,id',
            'currency' => 'required|integer|exists:currencies,id',
            'input_currency' => 'required|integer|exists:currencies,id',
            'due_date' => 'required|custom_date',
            'delivery_date' => 'nullable|custom_date',
            'tax' => 'required|in:T,F',
            'advance' => 'required|in:T,F',
            'show_model' => 'required|in:T,F',
            'model' => 'required|model',
            'reference_number' => 'required|reference_number',
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
            $rules['id'] = ['required', 'integer', Rule::exists('invoices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('retail', '=', 'F')->where('reversed_id', '=', 0)
                    ->where(function($query2) {
                        $query2->where('paid', '=', 'F')->orWhereNotNull('partial_paid_sum');
                    });
                })];
            $rules['client'] = ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
            $rules['date'] = 'required|date_time';
            $rules['status'] = 'required|in:1,2,3';

            if ($status == 3)
            {
                $rules['partial_paid_sum'] = 'required|decimal';
            }

            $rules['products.*.ip_id'] = ['nullable', 'integer', Rule::exists('invoice_products', 'id')
                ->where(function($query) use ($id) {
                    $query->where('invoice_id', '=', $id); })];
        }
        else
        {
            $rules['retail'] = 'required|in:T,F';
            $rules['office'] = ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
            $rules['register'] = ['required', 'integer', Rule::exists('registers', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];

            if ($retail == 'F')
            {
                $rules['client'] = ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                    $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })];
            }
            else
            {
                $rules['client'] = 'required|integer';
            }

            $rules['merchandise'] = 'required|in:T,F';
            $rules['print'] = 'required|in:T,F';
            $rules['email'] = 'required|in:T,F';
        }

        return $rules;
    }
}

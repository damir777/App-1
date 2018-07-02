<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Contract extends Model
{
    public $timestamps = false;

    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    //form validation - insert/update contract
    public static function validateContractForm($company_id, $id = false)
    {
        $rules = [
            'office' => ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'register' => ['required', 'integer', Rule::exists('registers', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'contract_number' => 'required',
            'client' => ['required', 'integer', Rule::exists('clients', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('deleted', '=', 'F'); })],
            'language' => 'required|integer|exists:languages,id',
            'payment_type' => 'required|integer|exists:payment_types,id',
            'currency' => 'required|integer|exists:currencies,id',
            'input_currency' => 'required|integer|exists:currencies,id',
            'due_days' => 'required|integer|min:1',
            'tax' => 'required|in:T,F',
            'number_of_invoices' => 'required|integer|min:1',
            'create_day' => 'required|integer|min:1',
            'previous_month_create' => 'required|in:T,F',
            'create_after_end' => 'required|in:T,F',
            'email_sending' => 'required|in:T,F',
            'active'=>'required|in:T,F',
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
            $rules['id'] = ['required', 'integer', Rule::exists('contracts', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id); })];
            $rules['products.*.cp_id'] = ['nullable', 'integer', Rule::exists('contract_products', 'id')
                ->where(function($query) use ($id) {
                    $query->where('contract_id', '=', $id); })];
        }

        return $rules;
    }
}

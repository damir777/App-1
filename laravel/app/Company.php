<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //form validation - fiscal certificate
    public static $fiscal_certificate = [
        'certificate' => 'mimes:bin',
        'password' => 'required'
    ];

    //form validation - company info
    public static $company_info = [
        'name' => 'required',
        'email' => 'required|email',
        'oib' => 'required|oib',
        'address' => 'required',
        'city' => 'required',
        'zip_code' => 'required',
        'phone' => 'required',
        'bank_account' => 'required',
        'iban' => 'required',
        'document_footer' => 'required',
        'pdv_user' => 'required|in:T,F',
        'sljednost_prostor' => 'required|in:T,F',
        'legal_form' => 'required|in:1,2'
    ];

    //form validation - logo
    public static $logo = [
        'logo' => 'required'
    ];

    //form validation - licence
    public static $licence = [
        'company_id' => 'required|integer|exists:companies,id',
        'licence_end' => 'required|custom_date'
    ];
}

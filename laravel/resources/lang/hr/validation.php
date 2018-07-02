<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'after_or_equal'       => 'The :attribute must be a date after or equal to :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'before_or_equal'      => 'The :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'Lozinka mora biti potvđena',
    'date'                 => 'The :attribute is not a valid date.',
    'date_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => ':attribute mora imati :digits znamenke',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => ':attribute mora biti validna email adresa',
    'exists'               => ':attribute nije validan/validna',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field must have a value.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => ':attribute nije validan/validna',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => ':attribute mora biti prirodni broj',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => ':attribute nema ispravan tip datoteke',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => ':attribute ne može biti veći od :max.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => ':attribute mora biti najmanje :min znakova.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => ':attribute je obavezan podatak',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => ':attribute je obavezno polje osim ako je :other jednaka :values',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => ':attribute se već koristi',
    'uploaded'             => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute format is invalid.',
    'decimal'              => ':attribute mora biti decimalni broj',
    'oib'                  => ':attribute mora imati 11 znamenki',
    'custom_date'          => ':attribute nije validan datum',
    'date_time'            => 'Unos datuma i vremena nije validan',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'g-recaptcha-response' => [
            'required' => 'Molimo potvrdite da niste robot',
            'captcha' => 'Greška validacije korisnika! Molimo pokušajte ponovno'
        ],
        'products.*.id' => [
            'required' => 'ID proizvoda je obavezan podatak',
            'integer' => 'ID proizvoda mora biti prirodni broj',
            'exists' => 'ID proizvoda nije validan'
        ],
        'products.*.quantity' => [
            'required' => 'Količina je obavezan podatak',
            'decimal' => 'Količina mora biti decimalni broj'
        ],
        'products.*.price' => [
            'required' => 'Cijena je obavezan podatak',
            'decimal' => 'Cijena mora biti decimalni broj'
        ],
        'products.*.custom_price' => [
            'required' => 'Cijena je obavezan podatak',
            'in' => 'Cijena nije validna'
        ],
        'products.*.brutto' => [
            'required' => 'Brutto je obavezan podatak',
            'in' => 'Brutto nije validan'
        ],
        'products.*.rebate' => [
            'required' => 'Rabat je obavezan podatak',
            'decimal' => 'Rabat mora biti decimalni broj'
        ],
        'products.*.op_id' => [
            'required' => 'ID proizvoda je obavezan podatak',
            'integer' => 'ID proizvoda mora biti prirodni broj',
            'exists' => 'ID proizvoda nije validan'
        ],
        'products.*.ip_id' => [
            'required' => 'ID proizvoda je obavezan podatak',
            'integer' => 'ID proizvoda mora biti prirodni broj',
            'exists' => 'ID proizvoda nije validan'
        ],
        'products.*.dp_id' => [
            'required' => 'ID proizvoda je obavezan podatak',
            'integer' => 'ID proizvoda mora biti prirodni broj',
            'exists' => 'ID proizvoda nije validan'
        ],
        'products.*.cp_id' => [
            'required' => 'ID proizvoda je obavezan podatak',
            'integer' => 'ID proizvoda mora biti prirodni broj',
            'exists' => 'ID proizvoda nije validan'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => ['email' => 'Email', 'password' => 'Lozinka', 'password_confirmation' => 'Potvrda lozinke',
        'first_name' => 'Ime', 'last_name' => 'Prezime', 'company_name' => 'Naziv poduzeća', 'office' => 'Poslovnica',
        'register' => 'Naplatni uređaj', 'job_title' => 'Radno mjesto', 'label' => 'Oznaka', 'certificate' => 'Certifikat',
        'text' => 'Tekst', 'category' => 'Kategorija', 'code' => 'Šifra', 'name' => 'Naziv', 'unit' => 'Jedinica mjere',
        'price' => 'Cijena', 'tax_group' => 'Porezna grupa', 'service' => 'Tip proizvoda', 'client_type'  => 'Tip klijenta',
        'oib' => 'OIB', 'tax_number' => 'Porezni broj', 'address' => 'Adresa', 'city' => 'Grad',
        'zip_code_text' => 'Poštanski broj', 'zip_code_select' => 'Poštanski broj', 'country' => 'Država',
        'int_client' => 'Oznaka stranog klijenta', 'vehicle_type' => 'Tip vozila', 'register_number' => 'Registracija',
        'year' => 'Godina', 'km' => 'Kilometraža', 'phone' => 'Telefon', 'zip_code' => 'Poštanski broj',
        'rebate' => 'Rabat', 'bank_account' => 'Žiro račun', 'iban' => 'IBAN', 'document_footer' => 'Podnožje ponuda/računa',
        'pdv_user' => 'Korisnik u sustavu PDV-a', 'sljednost_prostor' => 'Oznaka sljednosti', 'legal_form' => 'Pravni oblik',
        'client' => 'Klijent', 'language' => 'Jezik', 'payment_type' => 'Način plaćanja', 'currency' => 'Valuta',
        'input_currency' => 'Valuta unosa', 'date' => 'Datum', 'valid_date' => 'Vrijedi do', 'tax' => 'PDV',
        'products' => 'Proizvod', 'id' => 'ID', 'create_invoice' => 'Izradi račun', 'due_date' => 'Valuta',
        'merchandise' => 'Izrada otpremice', 'retail' => 'Tip računa', 'delivery_date' => 'Datum isporuke', 'advance' => 'Predujam',
        'show_model' => 'Prikaži model', 'model' => 'Model', 'reference_number' => 'Poziv na broj', 'status' => 'Status',
        'partial_paid_sum' => 'Uplaćeni iznos', 'show_prices' => 'Prikaži cijene', 'contract_number' => 'Broj ugovora',
        'due_days' => 'Valuta', 'number_of_invoices' => 'Trajanje ugovora', 'create_day' => 'Dan kreiranja računa',
        'previous_month_create' => 'Vrsta naplate', 'create_after_end' => 'Kreiraj nakon završetka ugovora',
        'email_sending' => 'Slanje e-mailom', 'active' => 'Aktivan', 'payer' => 'Uplatitelj', 'item' => 'Stavka', 'sum' => 'Iznos',
        'employee' => 'Zaposlenik', 'income' => 'Uplata utrška', 'start_date' => 'Početni datum', 'end_date' => 'Završni datum',
        'creator' => 'Naredbodavac', 'user' => 'Zaposlenik', 'vehicle' => 'Prijevozno sredstvo',
        'start_mileage' => 'Početna kilometraža', 'end_mileage' => 'Završna kilometraža', 'duration' => 'Trajanje',
        'location' => 'Lokacija', 'purpose' => 'Svrha putovanja', 'description' => 'Opis', 'non_costs' => 'Nepriznati troškovi',
        'wage' => 'Dnevnica', 'wage_type' => 'Tip dnevnice', 'start_time' => 'Početno vrijeme', 'end_time' => 'Završno vrijeme',
        'transport_type' => 'Vrsta prijevoza', 'start_location' => 'Početna lokacija', 'end_location' => 'Završna lokacija',
        'distance' => 'Kilometraža', 'km_price' => 'Cijena/km', 'cost_type' => 'Vrsta troška']

];

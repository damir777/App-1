<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Language;
use App\Currency;
use App\PaymentType;
use App\Unit;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | User roles
        |--------------------------------------------------------------------------
        */

        //insert user roles
        $roles = [['name' => 'SuperAdmin', 'display_name' => 'Super Admin'], ['name' => 'Admin',
            'display_name' => 'Admin'], ['name' => 'User', 'display_name' => 'User']];

        Role::insert($roles);

        /*
        |--------------------------------------------------------------------------
        | Languages
        |--------------------------------------------------------------------------
        */

        //insert languages
        $languages = [['code' => 'hr'], ['code' => 'en']];

        Language::insert($languages);

        /*
        |--------------------------------------------------------------------------
        | Currencies
        |--------------------------------------------------------------------------
        */

        //insert currencies
        $currencies = [['code' => 'HRK'], ['code' => 'EUR'], ['code' => 'USD'], ['code' => 'CHF'], ['code' => 'GBP']];

        Currency::insert($currencies);

        /*
        |--------------------------------------------------------------------------
        | Payment types
        |--------------------------------------------------------------------------
        */

        //insert payment types
        $payment_types = [['code' => 'cash'], ['code' => 'virman'], ['code' => 'bank_transfer'], ['code' => 'credit_card'],
            ['code' => 'cheque'], ['code' => 'other']];

        PaymentType::insert($payment_types);

        /*
        |--------------------------------------------------------------------------
        | Units
        |--------------------------------------------------------------------------
        */

        //insert units
        $units = [['code' => 'kom'],
            ['code' => 'sat'],
            ['code' => 'dan'],
            ['code' => 'mj'],
            ['code' => 'god'],
            ['code' => 'par'],
            ['code' => 'kart'],
            ['code' => 'set'],
            ['code' => 'pak'],
            ['code' => 'm'],
            ['code' => 'm2'],
            ['code' => 'm3'],
            ['code' => 'km'],
            ['code' => 'lit'],
            ['code' => 'kg'],
            ['code' => 't'],
            ['code' => 'kwh'],
            ['code' => 'paus'],
            ['code' => 'rata'],
            ['code' => 'komplet'],
            ['code' => 'usluga'],
            ['code' => 'vreca'],
            ['code' => 'doza'],
            ['code' => 'kutija'],
            ['code' => 'min']];

        Unit::insert($units);
    }
}

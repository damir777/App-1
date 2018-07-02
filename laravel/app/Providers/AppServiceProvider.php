<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Support\Facades\Validator;
use App\Company;

class AppServiceProvider extends ServiceProvider
{
    //set menu variable
    private $menu = 'menu.user';

    //set username variable
    private $username;

    //set user role variable
    private $user_role;

    //set user role id variable
    private $user_role_id = 3;

    //set company_name variable
    private $company_name;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*
        |--------------------------------------------------------------------------
        | Share menu view, username, user role, user role id and company name with all views
        |--------------------------------------------------------------------------
        */

        $this->app['events']->listen(Authenticated::class, function ($e) {

            //set default user role
            $this->user_role = trans('main.user');

            if ($e->user->hasRole('Admin'))
            {
                $this->menu = 'menu.admin';
                $this->user_role = trans('main.admin');
                $this->user_role_id = 2;
            }
            elseif ($e->user->hasRole('SuperAdmin'))
            {
                $this->menu = 'menu.superAdmin';
                $this->user_role = trans('main.super_admin');
                $this->user_role_id = 1;
            }

            view()->share('menu', $this->menu);

            //set username
            $this->username = $e->user->first_name.' '.$e->user->last_name;

            //get company name
            $this->company_name = Company::find($e->user->company_id)->name;

            //share username, user role, user role id and company name with all views55
            view()->share('username', $this->username);
            view()->share('user_role', $this->user_role);
            view()->share('user_role_id', $this->user_role_id);
            view()->share('company_name', $this->company_name);
        });

        /*
        |--------------------------------------------------------------------------
        | Additional validation rules
        |--------------------------------------------------------------------------
        */

        Validator::extend('custom_date', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.$/', $value);
        });

        Validator::extend('date_time', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}\.\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}$/', $value);
        });

        Validator::extend('decimal', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[0-9]+(\.[0-9]+)?$/', $value);
        });

        Validator::extend('oib', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^[0-9]{11}$/', $value);
        });

        Validator::extend('model', function($attribute, $value, $parameters, $validator)
        {
            return preg_match('/^\HR[0-9]{2}$/', $value);
        });

        Validator::extend('reference_number', function($attribute, $value, $parameters, $validator)
        {
            if (strlen($value) > 22)
            {
                return false;
            }

            return preg_match('/^\d+(\-\d+)?(\-\d+)?$/', $value);
        });

        /*
        |--------------------------------------------------------------------------
        | Set default string length for SQL indexes
        |--------------------------------------------------------------------------
        */

        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

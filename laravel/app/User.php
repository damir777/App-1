<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laratrust\Traits\LaratrustUserTrait;
use Illuminate\Validation\Rule;

class User extends Authenticatable
{
    use Notifiable, LaratrustUserTrait;

    public function role()
    {
        return $this->hasOne('App\RoleUser', 'user_id', 'id');
    }

    //form validation - create new admin
    public static $admin = [
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'password_confirmation' => 'required|min:6',
        'first_name' => 'required',
        'last_name' => 'required',
        'company_name' => 'required',
        'phone' => 'required',
        'g-recaptcha-response' => 'required|captcha'
    ];

    //form validation - insert/update user
    public static function validateUserForm($company_id, $office, $register, $id = false, $password = false)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required'
        ];

        if ($office)
        {
            $rules['office'] = ['required', 'integer', Rule::exists('offices', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id); })];
        }

        if ($register)
        {
            $rules['register'] = ['required', 'integer', Rule::exists('registers', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id); })];
        }

        if ($id)
        {
            $rules['id'] = ['required', 'integer', Rule::exists('users', 'id')->where(function($query) use ($company_id) {
                $query->where('company_id', '=', $company_id)->where('active', '=', 'T')->where('id', '!=', 32); })];
            $rules['email'] = ['required', 'email', Rule::unique('users')->ignore($id)];

            if ($password)
            {
                $rules['password'] = 'required|min:6|confirmed';
                $rules['password_confirmation'] = 'required|min:6';
            }
        }
        else
        {
            $rules['email'] = 'required|email|unique:users';
            $rules['password'] = 'required|min:6|confirmed';
            $rules['password_confirmation'] = 'required|min:6';
        }

        return $rules;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}

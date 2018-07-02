<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\User;
use App\Repositories\GeneralRepository;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;

class AuthController extends Controller
{
    //set repo variable
    private $repo;

    public function __construct()
    {
        //set repo
        $this->repo = new CompanyRepository;
    }

    //get login page
    public function getLoginPage()
    {
        return view('auth.login');
    }

    //login user
    public function loginUser(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        //if login fails redirect to login page
        if (!Auth::attempt(['email' => $email, 'password' => $password, 'active' => 'T']))
        {
            return redirect()->route('LoginPage')->with('error_message', trans('errors.login_error'));
        }

        //call getUserHomePageRoute method from GeneralRepository to get user home page route
        $this->repo = new GeneralRepository;
        $route = $this->repo->getUserHomePageRoute();

        return redirect()->route($route);
    }

    //get register page
    public function getRegisterPage()
    {
        return view('auth.register');
    }

    //register user
    public function registerUser(Request $request)
    {
        $first_name = $request->first_name;
        $last_name = $request->last_name;
        $email = $request->email;
        $password = $request->password;
        $phone = $request->phone;
        $company_name = $request->company_name;
        $website = $request->website;

        //validate form inputs
        $validator = Validator::make($request->all(), User::$admin);

        //if form input is not correct return error message
        if (!$validator->passes())
        {
            return redirect()->route('RegisterPage')->withErrors($validator)
                ->with('error_message', trans('errors.validation_error'))->withInput();
        }

        //if terms checkbox is not checked return error message
        if (!$request->has('terms_checkbox'))
        {
            return redirect()->route('RegisterPage')->with('error_message', trans('errors.terms_checkbox'))->withInput();
        }

        //call createCompany method from CompanyRepository to create new company
        $response = $this->repo->createCompany($first_name, $last_name, $email, $password, $phone, $company_name, $website);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('RegisterPage')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('LoginPage')->with('info_message', trans('main.register_message'));
    }

    //confirm admin account
    public function confirmAccount($token)
    {
        //call confirmAccount method from UserRepository to confirm admin account
        $this->repo = new UserRepository;
        $response = $this->repo->confirmAccount($token);

        //if response status = '0' return error message
        if ($response['status'] == 0)
        {
            return redirect()->route('RegisterPage')->with('error_message', trans('errors.error'))->withInput();
        }

        return redirect()->route('LoginPage')->with('info_message', trans('main.register_complete'));
    }

    //logout user
    public function logoutUser()
    {
        //logout user
        Auth::logout();

        //clear all session variables
        Session::flush();

        //redirect to login page
        return redirect()->route('LoginPage');
    }

    //get licence info
    public function getLicenceInfo()
    {
        //call getLicenceInfo method from CompanyRepository to get licence info
        $response = $this->repo->getLicenceInfo();

        return view('app.licenceInfo', ['active' => $response['active'], 'days_remaining' => $response['days_remaining']]);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Company;
use App\Repositories\CompanyRepository;
use App\Repositories\UserRepository;

class Authentication
{
    public function handle($request, Closure $next)
    {
        if (!Auth::user())
        {
            if ($request->ajax() || $request->wantsJson())
            {
                //set login error flash
                Session::flash('warning_message', trans('errors.app_login_error'));

                return response()->json(['status' => 401]);
            }

            return redirect()->route('LoginPage')->with('warning_message', trans('errors.app_login_error'));
        }

        //get user
        $user = Auth::user();

        if (!$user->hasRole('SuperAdmin'))
        {
            //get current route
            $route = $request->route()->getName();

            //call getLicenceInfo method from CompanyRepository to get licence info
            $repo = new CompanyRepository;
            $response = $repo->getLicenceInfo();

            if ($response['active'] == 'T')
            {
                if ($response['show_licence_info'] == 'T')
                {
                    if (!Session::has('licence'))
                    {
                        //set licence session
                        Session::put('licence', 1);

                        if ($route != 'GetLicenceInfo')
                        {
                            return redirect()->route('GetLicenceInfo');
                        }

                        return redirect()->route('GetLicenceInfo');
                    }
                }

                if ($user->hasRole('Admin') && $route != 'GetLicenceInfo' && $route != 'CompanyInfo' &&
                    $route != 'UpdateCompanyInfo' && $route != 'UploadLogo')
                {
                    //call getCompanyId method from UserRepository to get company id
                    $repo = new UserRepository;
                    $company_id = $repo->getCompanyId();

                    //get company profile
                    $profile = Company::find($company_id)->profile;

                    if ($profile == 'F')
                    {
                        return redirect()->route('CompanyInfo')->with('info_message', trans('main.company_profile_message'));
                    }
                }
            }
            else
            {
                //clear licence session
                Session::forget('licence');

                if ($route != 'GetLicenceInfo')
                {
                    return redirect()->route('GetLicenceInfo');
                }
            }
        }

        return $next($request);
    }
}

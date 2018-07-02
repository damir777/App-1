<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

class GeneralRepository
{
    //get user home page route
    public function getUserHomePageRoute()
    {
        //set default route
        $route = 'GetOffers';

        //get user
        $user = Auth::user();

        if ($user->hasRole('Admin'))
        {
            $route = 'AdminStatistics';
        }
        elseif ($user->hasRole('SuperAdmin'))
        {
            $route = 'SuperAdminStatistics';
        }

        return $route;
    }
}

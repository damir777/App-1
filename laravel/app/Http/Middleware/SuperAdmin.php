<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
{
    public function handle($request, Closure $next)
    {
        //get user
        $user = Auth::user();

        if (!$user->hasRole('SuperAdmin'))
        {
            return redirect()->route('LoginPage')->with('error_message', trans('errors.user_role_error'));
        }

        return $next($request);
    }
}

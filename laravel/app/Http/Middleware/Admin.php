<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle($request, Closure $next)
    {
        //get user
        $user = Auth::user();

        if (!$user->hasRole('Admin'))
        {
            if ($request->ajax() || $request->wantsJson())
            {
                return response()->json(['status' => 0, 'error' => trans('errors.user_role_error')]);
            }

            return redirect()->route('LoginPage')->with('error_message', trans('errors.user_role_error'));
        }

        return $next($request);
    }
}

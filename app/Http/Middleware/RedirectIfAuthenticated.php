<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {

        //dd('In RedirectIfAuthenticated', $request); 
        if (Auth::guard($guard)->check()) {
            if (auth()->user()->role_id == 1 || auth()->user()->role_id == 2) {
                //return redirect()->route('dashboard.home');
                return redirect()->route('dashboard.home'); //If user session active then comes here
            }
            else{
                // \Auth::logout();
                //     return redirect()->route('login')->with('warning', 'Not customer or seller');
                return $next($request);
            }
        }
        //if Auth::guard($guard)->check() is false
        return $next($request); //If session is not their then code comes here
    }
}

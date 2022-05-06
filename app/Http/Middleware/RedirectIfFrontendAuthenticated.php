<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfFrontendAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next, $guard = null)
    {

        //dd('In RedirectIfFrontendAuthenticated', $request, auth()->user(), auth()->user()->role_id);
        if (Auth::guard($guard)->check()) {
            if (auth()->user()->role_id == 5) { //Customer
                //return redirect()->route('dashboard.home');
                return redirect()->route('customer.customer_dashboard'); //If user session active then comes here
            }
            elseif(auth()->user()->role_id == 6){
                return redirect()->route('seller.seller_dashboard');
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

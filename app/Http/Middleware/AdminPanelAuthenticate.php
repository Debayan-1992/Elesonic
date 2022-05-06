<?php

namespace App\Http\Middleware;

use Closure;

class AdminPanelAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!auth()->user()){return redirect()->route('admin_login_form');}
        if(\Auth::guard()->user()->role_id == 5 || \Auth::guard()->user()->role_id == 6){ //Customer, Seller
            abort(401);
            \Auth::logout();
            return redirect()
            ->route('admin_login_form')
            ->with('warning', 'Only admins and superadmins can access the admin panel');
        }
       
        // if (!$request->expectsJson()) { //Gets executed if accessing middleware checked route without authentication
        //     //dd('Not logged in');
        //     //return route('login');
        //     return redirect()->route('admin_login_form');
        // }       
        return $next($request);
    }
}

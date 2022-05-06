<?php

namespace App\Http\Middleware;

use Closure;

class FrontendAuthenticate
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
        //dd($request, !auth()->user());
        if(!auth()->user()){return redirect()->route('login');}
        if(\Auth::guard()->user()->role_id == 1 || \Auth::guard()->user()->role_id == 2){ //Admin, Superadmin
            \Auth::logout();
            return redirect()->route('login')->with('warning', 'Admins and superadmins cannot access the frontend');
        }
        // if (!$request->expectsJson()) { //Gets executed if accessing middleware checked route without authentications
        //     //dd('Not logged in');
        //     //return route('login');
        //     //return $next($request);
        //     return redirect()->route('login_post');
        // }
        return $next($request); 
    }
}
//  Not being used as seller redirects 
//  to seller routes and customer redirects 
//  to customer routes
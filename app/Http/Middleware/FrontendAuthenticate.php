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
        //dd("sdfgdg");
        if (!$request->expectsJson()) { //Gets executed if accessing middleware checked route without authentications
            //dd('Not logged in');
            //return route('login');
            return redirect()->route('login');
        }
        
        //return $next($request);
    }
}

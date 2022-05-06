<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        //After successful login it comes here
        if (!$request->expectsJson()) { //Gets executed if accessing middleware checked route without authentications
            //dd('asdfasfas');
            //return route('login');
            return redirect()->route('admin_login_form');
            //return redirect()->to('www.google.com');
        }
    }

    

    
}

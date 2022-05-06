<?php

namespace App\Http\Middleware;

use Closure;

class CheckUser
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
        //dd($request, $next, auth()->user());
        if(\Auth::guard($guard)->check()){
            if(\Auth::guard($guard)->user()->status == 0){
                if ($request->expectsJson()) {
                    return response()->json(['status' => 'Your account has been deactivated, to activate the account contact or write to us'], 400);
                } else{
                    \Auth::logout();
                    return redirect()->route('login')->with('warning', 'Your account has been deactivated, to activate the account contact or write to us');
                }
            }
            //dd(auth()->user());
            return $next($request);
        }

    }
}

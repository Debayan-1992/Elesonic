<?php

namespace App\Http\Middleware;

use Closure;

class SellerAuth
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
        if(!auth()->user()){return redirect()->route('login');}
        if(\Auth::guard()->user()->role_id == 6)
        {
            return $next($request);
        }
        else{ //Admin, Superadmin
            abort(401);
            \Auth::logout();
            return redirect()
            ->route('login')
            ->with('warning', 'Only Seller can access this portal');
        } 
    }
}

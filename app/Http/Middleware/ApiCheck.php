<?php

namespace App\Http\Middleware;

use Closure;

class ApiCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($post, Closure $next)
    {
        if (!\Request::is('api/auth/*')) {
            if (!$post->has('api_token')) {
                return response()->json(['status' => 401, 'statuscode' => 'ERR', 'message' => 'Unauthorized Action.']);
            }

            $user = \App\User::where('api_token', $post->api_token)->first();
            if(!$user){
                return response()->json(['status' => 401, 'statuscode' => 'ERR', 'message' => 'Security token mismatch.']);
            }

            if ($user->status != '1') {
                return response()->json(['status' => 400, 'statuscode' => 'ERR', 'message' => 'Your account has been suspended.']);
            }
        }

        return $next($post);
    }
}

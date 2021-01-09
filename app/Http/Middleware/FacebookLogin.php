<?php

namespace App\Http\Middleware;

use Closure;

class FacebookLogin
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
        if($request->session()->get('access_token')) {
            return $next($request);
        }

        return route('login');
    }
}

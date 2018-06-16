<?php

namespace App\Http\Middleware\Admin;

use Closure;
use Illuminate\Http\Response;

class UnauthorizedIfGuest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        auth()->setDefaultDriver('jwt');
        if (!\Auth::guard()->check()) {
            return response(trans('error.http.401'),Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}

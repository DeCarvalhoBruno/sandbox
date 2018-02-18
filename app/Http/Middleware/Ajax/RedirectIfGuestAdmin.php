<?php

namespace App\Http\Middleware\Ajax;

use Closure;
use Illuminate\Http\Response;

class RedirectIfGuestAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (!\Auth::guard()->check()) {
            return response(trans('error.http.401'),Response::HTTP_UNAUTHORIZED);
        }
        return $next($request);
    }
}

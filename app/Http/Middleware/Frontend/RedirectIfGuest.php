<?php

namespace App\Http\Middleware\Frontend;

use Closure;

class RedirectIfGuest
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
        if (!\Auth::guard()->check()) {
            return redirect()->guest(route_i18n('admin.login'));
        }
        return $next($request);
    }
}

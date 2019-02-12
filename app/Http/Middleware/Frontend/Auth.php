<?php

namespace App\Http\Middleware\Frontend;

use Closure;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::guard('web')->check()) {
            return $next($request);
        }
        return redirect()->guest(route_i18n('login'));
    }
}

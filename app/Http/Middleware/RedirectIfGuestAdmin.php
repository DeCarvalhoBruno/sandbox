<?php

namespace App\Http\Middleware;

use Closure;

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
            return redirect()->guest(route('admin.login'));
        }
        return $next($request);
    }
}

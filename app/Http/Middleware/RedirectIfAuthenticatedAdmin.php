<?php namespace App\Http\Middleware;

class RedirectIfAuthenticatedAdmin
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, \Closure $next)
    {
        if (\Auth::guard()->check()) {
            return redirect()->intended(route_i18n('admin.dashboard'));
        }

        return $next($request);
    }

}

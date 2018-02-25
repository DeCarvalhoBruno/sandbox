<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Auth\Middleware\Authorize as LaravelAuthorize;

class Authorize extends LaravelAuthorize
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next,$ability,$entity)
    {
        dd($ability,$entity);
        return $next($request);
    }


}

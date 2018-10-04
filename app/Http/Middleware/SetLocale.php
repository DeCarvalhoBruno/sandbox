<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;

class SetLocale
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
        if ($locale = $this->parseLocale($request)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return string|null
     */
    protected function parseLocale($request)
    {
//        $path = substr($request->getPathInfo(),1);
//        $urlLocale = substr($path,0,strpos($path, '/') ?: strlen($path));
        $locale = \Cookie::get('locale');
        if (is_null($locale)) {
            $locale = $request->server('HTTP_ACCEPT_LANGUAGE');
            $locale = substr($locale, 0, strpos($locale, ',') ?: strlen($locale));
            $locales = config('app.locales');
            if (array_key_exists($locale, $locales)) {
                Carbon::setLocale($locale);
                return $locale;
            }

            $locale = substr($locale, 0, 2);
            if (array_key_exists($locale, $locales)) {
                Carbon::setLocale($locale);
                return $locale;
            }
            $locale = config('app.locale');
        }
        Carbon::setLocale($locale);
        return $locale;
    }

    protected function parseFromRequest($request)
    {
        $locales = config('app.locales');

        $locale = $request->server('HTTP_ACCEPT_LANGUAGE');
        Carbon::setLocale($locale);
        $locale = substr($locale, 0, strpos($locale, ',') ?: strlen($locale));


    }

}

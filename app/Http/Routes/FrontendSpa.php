<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class FrontendSpa extends Routes
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => '/',
        ], call_user_func('static::defaultRouteGroup', null));
//        $availableLocales = config('app.locales');
//        unset($availableLocales[app()->getLocale()]);
//        foreach ($availableLocales as $k => $v) {
//            $router->group([
//                'prefix' => sprintf('/%s', $k),
//            ], call_user_func('static::defaultRouteGroup', $k));
//        }
    }

    public static function defaultRouteGroup($locale)
    {
        return function (Router $r) use ($locale) {
            $r->group([
                'namespace' => 'App\Http\Controllers\Frontend\Spa',
            ], call_user_func('static::guest', $locale));
            $r->group([
                'namespace' => 'App\Http\Controllers\Frontend\Spa',
                'middleware' => ['auth.admin', 'admin'],
            ], call_user_func('static::auth'));
            $r->group([
                'namespace' => 'App\Http\Controllers\Frontend\Spa',
                'middleware' => ['admin'],
            ], call_user_func('static::handledBySPA'));
        };
    }

    public static function guest($locale)
    {
        return function (Router $r) use ($locale) {
            $r->post('login', 'Auth\Login@login')->name('login.post');
            $r->post('logout', 'Auth\Login@logout')->name('logout');

            $r->post('register', 'Auth\Register@register')
                ->name(self::i18nRouteNames($locale, 'register.post'));

            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')
                ->name(self::i18nRouteNames($locale, 'password.email'));
            $r->post('password/reset', 'Auth\ResetPassword@reset');

        };
    }

    public static function handledBySPA()
    {
        return function (Router $r) {
            $r->get('{path}', function () {
                return view('frontend.default-spa');
            })->where('path', '(.*)');
        };
    }

    public static function auth()
    {
        return function (Router $r) {
            $r->post('logout', 'Auth\Login@logout');
        };
    }

}
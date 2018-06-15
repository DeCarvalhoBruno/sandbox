<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class Admin extends Routes
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => '/admin',
        ], call_user_func('static::defaultRouteGroup', null));
        $availableLocales = config('app.locales');
        unset($availableLocales[app()->getLocale()]);
        foreach ($availableLocales as $k => $v) {
            $router->group([
                'prefix' => sprintf('/%s/admin', $k),
            ], call_user_func('static::defaultRouteGroup', $k));
        }
    }

    public static function defaultRouteGroup($locale)
    {
        return function (Router $r) use ($locale) {
            $r->group([
                'namespace' => 'App\Http\Controllers\Admin',
            ], call_user_func('static::guest', $locale));
            $r->group([
                'middleware' => ['auth.spa','spa'],
                'namespace' => 'App\Http\Controllers\Admin',
            ], call_user_func('static::auth'));
            $r->group([
                'middleware' => ['spa'],
                'namespace' => 'App\Http\Controllers\Admin',
            ], call_user_func('static::handledBySPA'));
        };
    }

    public static function handledBySPA(){
        return function (Router $r) {
            $r->get('/{path}', 'Admin@index')->where('path', '(.*)');
        };
    }

    public static function auth()
    {
        return function (Router $r) {
            $r->post('logout', 'Auth\Login@logout');
        };
    }

    public static function guest($locale)
    {
        return function (Router $r) use ($locale) {
            $r->get('', 'Admin@index');
//            $r->get('dashboard', 'Admin@index')->name(self::i18nRouteNames($locale, 'admin.dashboard'));
            $r->post('login', 'Auth\Login@login')->name(self::i18nRouteNames($locale, 'admin.login'));
            $r->post('register', 'Auth\Register@register')->name(self::i18nRouteNames($locale, 'admin.register'));
            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')->name(self::i18nRouteNames($locale,
                'admin.password.email-reset'));
            $r->post('password/reset', 'Auth\ResetPassword@reset')->name(self::i18nRouteNames($locale,
                'admin.password.reset'));
            $r->get('/test', 'Admin@test');
        };
    }
}
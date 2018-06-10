<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class Admin
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
                'middleware' => ['ajax'],
                'namespace' => 'App\Http\Controllers\Admin',
            ], call_user_func('static::guestAjaxRoutes', $locale));
            $r->group([
                'middleware' => ['auth:ajax'],
                'namespace' => 'App\Http\Controllers\Ajax\Admin',
            ], call_user_func('static::authAjaxRoutes', $locale));
            $r->group([
                'middleware' => ['web'],
                'namespace' => 'App\Http\Controllers\Admin',
            ], call_user_func('static::guestWebRoutes'));
        };
    }

    public static function guestWebRoutes()
    {
        return function (Router $r) {
            $r->get('/test', 'Admin@test');
            $r->get('/{path}', 'Admin@index')->where('path', '(.*)');
        };
    }

    public static function guestAjaxRoutes($locale)
    {
        return function (Router $r) use ($locale) {
            $r->get('', 'Admin@index');
            $r->get('dashboard', 'Admin@index')->name(self::i18nRouteNames($locale, 'admin.dashboard'));
            $r->post('login', 'Auth\Login@login')->name(self::i18nRouteNames($locale, 'admin.login'));
            $r->post('register', 'Auth\Register@register')->name(self::i18nRouteNames($locale, 'admin.register'));
            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')->name(self::i18nRouteNames($locale,
                'admin.password.email-reset'));
            $r->post('password/reset', 'Auth\ResetPassword@reset')->name(self::i18nRouteNames($locale,
                'admin.password.reset'));
        };

    }

    public static function authAjaxRoutes($locale)
    {
        return function (Router $r) use ($locale) {
            $r->post('logout', 'Auth\Login@logout')->name(self::i18nRouteNames($locale, 'admin.logout'));
            $r->patch('settings/profile', 'Settings\Profile@update')->name(self::i18nRouteNames($locale,
                'admin.settings.profile'));
            $r->patch('settings/password', 'Settings\Password@update')->name(self::i18nRouteNames($locale,
                'admin.settings.password'));
        };
    }

    private static function i18nRouteNames($locale, $name)
    {
        if ($locale != null) {
            return sprintf('%s.%s', $locale, $name);
        }
        return $name;

    }
}
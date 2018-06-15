<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class User extends Routes
{
    public function bind(Router $router)
    {
//        $router->group([
//            'prefix' => '/',
//        ], call_user_func('static::defaultRouteGroup', null));
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
//            $r->group([
//                'middleware' => ['web'],
//                'namespace' => 'App\Http\Controllers\User',
//            ], call_user_func('static::guestRoutes', $locale));
//            $r->group([
//                'middleware' => ['auth:ajax'],
//                'namespace' => 'App\Http\Controllers\Ajax\User',
//            ], call_user_func('static::authRoutes', $locale));
//            $r->group([
//                'middleware' => ['web'],
//                'namespace' => 'App\Http\Controllers\Admin',
//            ], call_user_func('static::guestRoutes'));
        };
    }

    public static function guest($locale)
    {
        return function (Router $r) use ($locale) {
//            $r->get('', 'Admin@index');
//            $r->get('dashboard', 'Admin@index')->name(self::i18nRouteNames($locale, 'admin.dashboard'));
//            $r->post('login', 'Auth\Login@login')->name(self::i18nRouteNames($locale, 'admin.login'));
//            $r->post('register', 'Auth\Register@register')->name(self::i18nRouteNames($locale, 'admin.register'));
//            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')->name(self::i18nRouteNames($locale,
//                'admin.password.email-reset'));
//            $r->post('password/reset', 'Auth\ResetPassword@reset')->name(self::i18nRouteNames($locale,
//                'admin.password.reset'));
        };

    }

    public static function authRoutes($locale)
    {
        return function (Router $r) use ($locale) {
            $r->post('logout', 'Auth\Login@logout')->name(self::i18nRouteNames($locale, 'admin.logout'));
            $r->patch('settings/profile', 'Settings\Profile@update')->name(self::i18nRouteNames($locale,
                'admin.settings.profile'));
            $r->patch('settings/password', 'Settings\Password@update')->name(self::i18nRouteNames($locale,
                'admin.settings.password'));
        };
    }




}
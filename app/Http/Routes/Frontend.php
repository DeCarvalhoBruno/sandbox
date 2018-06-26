<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class Frontend extends Routes
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => '/',
            'middleware' => ['web'],
            'namespace' => 'App\Http\Controllers\Frontend',
        ], call_user_func('static::defaultRouteGroup', null));
        $availableLocales = config('app.locales');
        unset($availableLocales[app()->getLocale()]);
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
            ], call_user_func('static::guest', $locale));

            $r->group([
            ], call_user_func('static::auth', $locale));

//            $r->group([
//                'middleware' => ['web'],
//                'namespace' => 'App\Http\Controllers\Admin',
//            ], call_user_func('static::guestRoutes'));
        };
    }

    public static function guest($locale)
    {
        return function (Router $r) use ($locale) {
            $r->get('login', 'Auth\Login@index')
                ->name(self::i18nRouteNames($locale, 'login'));
            $r->get('activate/{token}', 'Auth\Login@activate')
                ->name(self::i18nRouteNames($locale, 'activate'));

            $r->post('login', 'Auth\Login@login')->name('login.post');
            $r->post('logout', 'Auth\Login@logout')->name('logout');

            $r->get('register', 'Auth\Register@showRegistrationForm')
                ->name(self::i18nRouteNames($locale, 'register'));
            $r->post('register', 'Auth\Register@register')
                ->name(self::i18nRouteNames($locale, 'register.post'));

            $r->get('password/reset', 'Auth\ForgotPassword@showLinkRequestForm')
                ->name(self::i18nRouteNames($locale, 'password.request'));
            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')
                ->name(self::i18nRouteNames($locale, 'password.email'));
            $r->get('password/reset/{token}', 'Auth\ResetPassword@showResetForm')
                ->name(self::i18nRouteNames($locale, 'password.reset'));
            $r->post('password/reset', 'Auth\ResetPassword@reset');

            $r->get('blog/{slug}', 'Blog@getPost')
                ->name(self::i18nRouteNames($locale, 'blog'));

        };

    }

    public static function auth($locale)
    {
        return function (Router $r) use ($locale) {
            $r->get('/test', 'Frontend@test');
            $r->get('/', 'Home@index')
                ->name(self::i18nRouteNames($locale, 'home'));
        };
    }

}
<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class Frontend extends Routes
{
    public function bind(Router $router)
    {
//        $router->group([
//            'prefix' => '/',
//            'middleware' => ['web'],
//            'namespace' => 'App\Http\Controllers\Frontend',
//        ], call_user_func('static::defaultRouteGroup', null));
        $availableLocales = config('app.locales');
        unset($availableLocales[app()->getLocale()]);
        $availableLocales[''] = '';
//        dd($availableLocales);
        foreach ($availableLocales as $k => $v) {
            $router->group([
                'prefix' => sprintf('/%s', $k),
                'middleware' => ['web'],
                'namespace' => 'App\Http\Controllers\Frontend',
            ], call_user_func('static::defaultRouteGroup', $k));
        }
    }

    public static function defaultRouteGroup($locale)
    {
        return function (Router $r) use ($locale) {
            $r->group([
            ], call_user_func('static::guest', $locale));

            $r->group([
            ], call_user_func('static::auth', $locale));

        };
    }

    public static function guest($locale)
    {
        return function (Router $r) use ($locale) {

            $r->get(trans('routes.home', [], $locale), 'Home@index')
                ->name(self::i18nRouteNames($locale, 'home'));

            $r->get(trans('routes.login', [], $locale), 'Auth\Login@index')
                ->name(self::i18nRouteNames($locale, 'login'));
            $r->get(trans('routes.activate', [], $locale), 'Auth\Login@activate')
                ->name(self::i18nRouteNames($locale, 'activate'));

            $r->post('login', 'Auth\Login@login')->name('login.post');

            $r->get(trans('routes.register', [], $locale), 'Auth\Register@showRegistrationForm')
                ->name(self::i18nRouteNames($locale, 'register'));
            $r->post('register', 'Auth\Register@register')
                ->name(self::i18nRouteNames($locale, 'register.do'));

            $r->get(trans('routes.password_reset', [], $locale), 'Auth\ForgotPassword@showLinkRequestForm')
                ->name(self::i18nRouteNames($locale, 'password.request'));
            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')
                ->name('password.email');
            $r->get(trans('routes.password_reset_token', [], $locale), 'Auth\ResetPassword@showResetForm')
                ->name(self::i18nRouteNames($locale, 'password.reset'));
            $r->post('password/reset', 'Auth\ResetPassword@reset')->name( 'password.reset.do');

//            $r->get(trans('routes.blog_slug', [], $locale), 'Blog@getPost')
//                ->name(self::i18nRouteNames($locale, 'blog'));

        };

    }

    public static function auth($locale)
    {
        return function (Router $r) use ($locale) {
            $r->post('logout', 'Auth\Login@logout')->name('logout');
            $r->get(trans('routes.user_profile', [], $locale), 'User@edit')
                ->name(self::i18nRouteNames($locale, 'profile'));

        };
    }

}
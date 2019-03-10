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
                'middleware' => ['frontend_auth'],
            ], call_user_func('static::auth', $locale));
        };
    }

    public static function guest($locale)
    {
        return function (Router $r) use ($locale) {

            $r->get(trans('routes.home', [], $locale), 'Home@index')
                ->name(self::i18nRouteNames($locale, 'home'));

            $r->get(trans('routes.login', [], $locale), 'Auth\Login@index')
                ->name(self::i18nRouteNames($locale, 'login'))->middleware('frontend_guest');
            $r->get(trans('routes.activate', [], $locale), 'Auth\Login@activate')
                ->name(self::i18nRouteNames($locale, 'activate'));

            $r->post('login', 'Auth\Login@login')->name('login.post');
            $r->post('oauth/{driver}', 'Auth\OAuth@redirectToProvider')->name('oauth');
            $r->get('oauth/{driver}/callback', 'Auth\OAuth@handleProviderCallback')->name('oauth.callback');
            $r->post('oauth-yolo', 'Auth\OAuth@googleYolo');

            $r->get(trans('routes.register', [], $locale), 'Auth\Register@showRegistrationForm')
                ->name(self::i18nRouteNames($locale, 'register'));
            $r->post('register', 'Auth\Register@register')->name('register.do');

            $r->get(
                trans('routes.password_reset', [], $locale),
                'Auth\ForgotPassword@showLinkRequestForm'
            )->name(self::i18nRouteNames($locale, 'password.request'));
            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')
                ->name('password.email');
            $r->get(
                trans('routes.password_reset_token', [], $locale),
                'Auth\ResetPassword@showResetForm'
            )->name(self::i18nRouteNames($locale, 'password.reset'));
            $r->post('password/reset', 'Auth\ResetPassword@reset')->name( 'password.reset.do');
            $r->get(trans('routes.contact', [], $locale), 'Frontend@contact')
                ->name(self::i18nRouteNames($locale, 'contact'));
            $r->post('contact/send', 'Frontend@sendContactEmail')->name( 'contact.send');

            $r->get(trans('routes.blog_slug', [], $locale), 'Blog@getPost')
                ->name(self::i18nRouteNames($locale, 'blog'));
            $r->get(trans('routes.blog_cat', [], $locale), 'Blog@category')
                ->name(self::i18nRouteNames($locale, 'blog.category'));
            $r->get(trans('routes.blog_tag', [], $locale), 'Blog@tag')
                ->name(self::i18nRouteNames($locale, 'blog.tag'));
            $r->get(trans('routes.blog_author', [], $locale), 'Blog@author')
                ->name(self::i18nRouteNames($locale, 'blog.author'));

            $r->get(trans('routes.search', [], $locale), 'Search@get')
                ->name(self::i18nRouteNames($locale, 'search'));
            $r->post('search','Search@post');

            $r->post('email/subscribe/newsletter', 'Frontend@newsletterSubscribe')
                ->name( 'subscribe_newsletter');

            $r->get(trans('routes.privacy', [], $locale), 'Frontend@privacy')
                ->name(self::i18nRouteNames($locale, 'privacy'));
            $r->get(trans('routes.terms_service', [], $locale), 'Frontend@termsOfService')
                ->name(self::i18nRouteNames($locale, 'terms.service'));
        };

    }

    public static function auth($locale)
    {
        return function (Router $r) use ($locale) {
            $r->post('logout', 'Auth\Login@logout')->name('logout');
            $r->get(trans('routes.settings_profile', [], $locale), 'Settings\Profile@edit')
                ->name(self::i18nRouteNames($locale, 'profile'));
            $r->post('settings/profile/update', 'Settings\Profile@update')
                ->name('profile.update');
            $r->get(trans('routes.settings_notifications', [], $locale), 'Settings\Notifications@edit')
                ->name(self::i18nRouteNames($locale, 'notifications'));
            $r->get(trans('routes.settings_account', [], $locale), 'Settings\Account@edit')
                ->name(self::i18nRouteNames($locale, 'account'));

        };
    }

}
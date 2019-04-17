<?php namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function map()
    {
        $router = $this->app->make(Router::class);
        $availableLocales = config('app.locales');
        unset($availableLocales[app()->getLocale()]);
        $availableLocales[''] = '';
        foreach ($availableLocales as $locale => $v) {
            $router->group([
                'prefix' => sprintf('/%s', $locale),
                'namespace' => 'App\Http\Controllers',
                'middleware' => ['web'],
            ], function () use ($router, $locale) {
                $router->get(trans('routes.blog_slug', [], $locale), 'Blog@getPost')
                    ->name(i18nRouteNames($locale, 'blog'));
                $router->get(trans('routes.blog_cat', [], $locale), 'Blog@category')
                    ->name(i18nRouteNames($locale, 'blog.category'));
                $router->get(trans('routes.blog_tag', [], $locale), 'Blog@tag')
                    ->name(i18nRouteNames($locale, 'blog.tag'));
                $router->get(trans('routes.blog_author', [], $locale), 'Blog@author')
                    ->name(i18nRouteNames($locale, 'blog.author'));

                $router->get(trans('routes.home', [], $locale), 'Home@index')
                    ->name(i18nRouteNames($locale, 'home'));
            });
        }

    }

}
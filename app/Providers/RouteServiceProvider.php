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
        foreach ($availableLocales as $k => $v) {
            $router->group([
                'prefix' => sprintf('/%s', $k),
                'namespace'=>'App\Http\Controllers',
                'middleware' => ['web'],
            ], function () use ($router,$k) {
                $router->get(trans('routes.blog_slug', [], $k), 'Blog@getPost')
                    ->name(i18nRouteNames($k, 'blog'));
                $router->get(trans('routes.blog_cat', [], $k), 'Blog@category')
                    ->name(i18nRouteNames($k, 'blog.category'));
                $router->get(trans('routes.blog_tag', [], $k), 'Blog@tag')
                    ->name(i18nRouteNames($k, 'blog.tag'));
                $router->get(trans('routes.blog_author', [], $k), 'Blog@author')
                    ->name(i18nRouteNames($k, 'blog.author'));
            });
        }

    }

}
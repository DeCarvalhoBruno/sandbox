<?php namespace Naraki\Blog;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Naraki\Blog\Composers\Blog as BlogComposer;
use Naraki\Blog\Contracts\Blog;

class ServiceProvider extends LaravelServiceProvider
{
    private $routeSets = [
        Routes\Admin::class,
        Routes\Frontend::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Contracts\BlogCategory::class, Providers\BlogCategory::class);
        $this->app->singleton(Contracts\BlogTag::class, Providers\BlogTag::class);
        $this->app->singleton(Contracts\BlogSource::class, Providers\BlogSource::class);
        $this->app->singleton(Contracts\Blog::class, Providers\Blog::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'blog');
        $this->app->make('view')->composer(['blog::post'], BlogComposer::class);

        $router = $this->app->make(Router::class);
        foreach ($this->routeSets as $binder) {
            $this->app->make($binder)->bind($router);
        }
    }
}
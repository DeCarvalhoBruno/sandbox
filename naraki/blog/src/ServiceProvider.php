<?php namespace Naraki\Blog;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Naraki\Blog\Composers\Blog as BlogComposer;

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
        $this->app->singleton(Contracts\Category::class, Providers\Category::class);
        $this->app->singleton(Contracts\Tag::class, Providers\Tag::class);
        $this->app->singleton(Contracts\Source::class, Providers\Source::class);
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
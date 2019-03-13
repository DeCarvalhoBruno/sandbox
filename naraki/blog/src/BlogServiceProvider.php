<?php namespace Naraki\Blog;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Routing\Router;
use Naraki\Blog\Providers\Blog as BlogProvider;
use Naraki\Blog\Composers\Blog as BlogComposer;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

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
        $this->app->singleton(Contracts\Blog::class, BlogProvider::class);
        $this->app->alias(BlogProvider::class, 'blog');

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
<?php namespace Naraki\Blog;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Naraki\Blog\Providers\Blog;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Contracts\Blog::class, Providers\Blog::class);
        $this->app->bind(Contracts\BlogCategory::class, Providers\BlogCategory::class);
        $this->app->bind(Contracts\BlogTag::class, Providers\BlogTag::class);
        $this->app->bind(Contracts\BlogSource::class, Providers\BlogSource::class);
        $this->app->singleton(Blog::class, function () {
            $instance = new Providers\Blog;
        });
        $this->app->alias(Blog::class, 'blog');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
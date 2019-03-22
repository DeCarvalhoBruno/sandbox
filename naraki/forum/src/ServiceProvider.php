<?php namespace Naraki\Forum;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Contracts\Board::class, Providers\Board::class);
        $this->app->singleton(Contracts\Thread::class, Providers\Thread::class);
        $this->app->singleton(Contracts\Post::class, Providers\Post::class);
        $this->app->singleton(Contracts\Forum::class, Providers\Forum::class);

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['router']->group([
            'prefix' => '/ajax/',
            'namespace' => 'Naraki\Forum\Controllers',
            'middleware' => ['frontend_auth'],
            'guard' => 'web'
        ], function (Router $r) {
            $r->get('forum/{entity_type}/{slug}/comment', 'Post@getComments');
            $r->post('forum/{entity_type}/{slug}/comment', 'Post@postComment');

        });


    }

}
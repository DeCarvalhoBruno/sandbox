<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Naraki\Core\Controllers';

    /**
     * Define the routes for the application.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function map()
    {
        if (config('app.env') === 'local') {
            $router = $this->app->make(Router::class);
            $router->group(['namespace' => '\Rap2hpoutre\LaravelLogViewer'], function () use ($router) {
                $router->get('logs', 'LogViewerController@index');
            });
        }
    }
}

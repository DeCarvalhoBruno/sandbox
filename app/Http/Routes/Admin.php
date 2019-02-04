<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class Admin extends Routes
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => '/admin',
                'namespace' => 'App\Http\Controllers\Admin',
        ], call_user_func('static::defaultRouteGroup'));
    }

    public static function defaultRouteGroup()
    {
        return function (Router $r) {
            $r->group([], call_user_func('static::guest'));
            $r->group([
                'middleware' => ['auth.admin','admin'],
            ], call_user_func('static::auth'));
            $r->group([
                'middleware' => ['admin'],
            ], call_user_func('static::handledBySPA'));
        };
    }

    public static function guest()
    {
        return function (Router $r) {
            $r->get('', 'Admin@index');
            $r->post('login', 'Auth\Login@login')->name('admin.login');
        };
    }

    public static function handledBySPA(){
        return function (Router $r) {
            $r->get('/{path}', 'Admin@index')->where('path', '(.*)');
        };
    }

    public static function auth()
    {
        return function (Router $r) {
            $r->post('logout', 'Auth\Login@logout');
        };
    }
}
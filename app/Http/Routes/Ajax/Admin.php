<?php namespace App\Http\Routes\Ajax;

use Illuminate\Routing\Router;

class Admin
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => 'ajax/admin',
            'namespace' => 'App\Http\Controllers\Ajax\Admin',
        ],
            function (Router $r) {
                $r->group([
                    'middleware' => ['auth:ajax']
                ], call_user_func('static::authAjaxRoutes'));
            }
        );
    }

    public static function authAjaxRoutes()
    {
        return function (Router $r) {
            $r->get('users', 'User@index')->name('admin.user.index');
        };
    }
}
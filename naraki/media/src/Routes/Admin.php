<?php namespace Naraki\Media\Routes;

use Illuminate\Routing\Router;

class Admin
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => '/ajax/admin',
            'namespace' => 'Naraki\Media\Controllers',
        ],
            function (Router $r) {
                $r->group([
                    'middleware' => ['admin_auth','admin']
                ], call_user_func('static::routes'));
            }
        );
    }

    public static function routes()
    {
        return function (Router $r) {
            $r->get('media/{media}', 'Media@edit');
            $r->patch('media/{media}', 'Media@update');
            $r->post('media/add', 'Media@add')->middleware('can:edit,App\Models\User');
            $r->post('media/crop', 'Media@crop')->middleware('can:edit,App\Models\User');
        };
    }
}
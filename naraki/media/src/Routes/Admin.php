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
            $r->get('media/{entity}/{media}', 'Media@edit');
            $r->get('media', 'Media@index');
            $r->patch('media/{media}', 'Media@update');
            $r->post('media/add', 'Media@add')->middleware('can:edit,App\Models\User');
            $r->post('media/crop/avatar', 'Media@cropAvatar')->middleware('can:edit,App\Models\User');
            $r->post('media/crop/image', 'Media@crop');
        };
    }
}
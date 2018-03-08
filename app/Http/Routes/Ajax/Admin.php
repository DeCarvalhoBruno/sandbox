<?php namespace App\Http\Routes\Ajax;

use Illuminate\Routing\Router;

class Admin
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => '/ajax/admin',
            'namespace' => 'App\Http\Controllers\Ajax\Admin',
        ],
            function (Router $r) {
                $r->group([
                    'middleware' => 'auth.ajax:ajax'
                ], call_user_func('static::authAjaxRoutes'));
            }
        );
    }

    public static function authAjaxRoutes()
    {
        return function (Router $r) {
            $r->get('users', 'User@index')->middleware('can:view,App\Models\User');
            $r->get('users/{user}', 'User@edit')->middleware('can:update,App\Models\User');
            $r->patch('users/{user}', 'User@update')->middleware('can:update,App\Models\User');
            $r->delete('users/{user}', 'User@destroy')->middleware('can:delete,App\Models\User');
            $r->get('users/search/{search}', 'User@search')->middleware('can:view,App\Models\User');

            $r->get('groups', 'Group@index')->middleware('can:view,App\Models\Group');
            $r->get('groups/{group}', 'Group@edit')->middleware('can:update,App\Models\Group');
            $r->patch('groups/{group}', 'Group@update')->middleware('can:update,App\Models\Group');

            $r->get('members/{group}/search/{search}', 'GroupMember@search')->middleware('can:view,App\Models\Group');
            $r->get('members/{group}', 'GroupMember@index')->middleware('can:view,App\Models\Group');
            $r->patch('members/{group}', 'GroupMember@update')->middleware('can:view,App\Models\Group');
        };
    }
}
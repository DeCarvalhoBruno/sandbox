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
                    'middleware' => ['admin_auth','admin']
                ], call_user_func('static::routes'));
            }
        );
    }

    public static function routes()
    {
        return function (Router $r) {
            $r->get('users', 'User@index')
                ->middleware('can:view,App\Models\User');
            $r->get('users/profile', 'User@profile');
            $r->get('users/{user}', 'User@edit')
                ->middleware('can:edit,App\Models\User');
            $r->patch('users/{user}', 'User@update')
                ->middleware('can:delete,App\Models\User');
            $r->delete('users/{user}', 'User@destroy')
                ->middleware('can:delete,App\Models\User');
            $r->post('users/batch/delete', 'User@batchDestroy')
                ->middleware('can:delete,App\Models\User');
            $r->get('users/search/{search}/{limit}', 'User@search')
                ->middleware('can:view,App\Models\User');
            $r->get('people/search/{search}/{limit}', 'Person@search')
                ->middleware('can:view,App\Models\User');

            $r->get('groups', 'Group@index')
                ->middleware('can:view,App\Models\Group');
            $r->get('groups/create', 'Group@add')
                ->middleware('can:add,App\Models\Group');
            $r->post('groups', 'Group@create')
                ->middleware('can:add,App\Models\Group');
            $r->get('groups/{group}', 'Group@edit')
                ->middleware('can:edit,App\Models\Group');
            $r->patch('groups/{group}', 'Group@update')
                ->middleware('can:edit,App\Models\Group');
            $r->delete('groups/{group}', 'Group@destroy')
                ->middleware('can:delete,App\Models\Group');

            $r->get('members/{group}/search/{search}/{limit}', 'GroupMember@search')
                ->middleware('can:view,App\Models\Group');
            $r->get('members/{group}', 'GroupMember@index')
                ->middleware('can:view,App\Models\Group');
            $r->patch('members/{group}', 'GroupMember@update')
                ->middleware('can:view,App\Models\Group');

            $r->get('user/general', 'User\General@edit');
            $r->patch('user/general', 'User\General@update');
            $r->patch('user/password', 'User\Password@update');
            $r->patch('user/profile', 'User\Profile@update');
            $r->get('user/avatar', 'User\Profile@avatar');
            $r->patch('user/avatar', 'User\Profile@setAvatar');
            $r->delete('user/avatar/{uuid}', 'User\Profile@deleteAvatar');

            $r->get('settings/general','Settings@edit');
            $r->post('settings/general','Settings@update');
            $r->get('settings/social','Settings@editSocial');
            $r->post('settings/social','Settings@updateSocial');
            $r->get('settings/sitemap','Settings@editSitemap');
            $r->post('settings/sitemap','Settings@updateSitemap');

            $r->get('dashboard', 'Dashboard@index');

            $r->get('system/events/log', 'System@getLog');
        };
    }
}
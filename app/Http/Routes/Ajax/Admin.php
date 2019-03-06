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

            $r->get('blog/categories', 'BlogCategory@index');
            $r->post('blog/categories', 'BlogCategory@create');
            $r->patch('blog/categories/{id}', 'BlogCategory@update');
            $r->delete('blog/categories/{id}', 'BlogCategory@delete');

            $r->get('blog/posts', 'Blog@index')
                ->middleware('can:view,App\Models\Blog\BlogPost');
            $r->get('blog/post/create', 'Blog@add')
                ->middleware('can:add,App\Models\Blog\BlogPost');
            $r->post('blog/post/create', 'Blog@create')
                ->middleware('can:add,App\Models\Blog\BlogPost');
            $r->get('blog/post/edit/{slug}', 'Blog@edit')
                ->middleware('can:edit,App\Models\Blog\BlogPost');
            $r->post('blog/post/edit/{slug}', 'Blog@update')
                ->middleware('can:edit,App\Models\Blog\BlogPost');
            $r->delete('blog/post/{slug}', 'Blog@destroy');
            $r->post('blog/post/batch/delete', 'Blog@batchDestroy');
            $r->post('blog/post/source/create', 'BlogSource@create');
            $r->delete('blog/post/source/delete/{id}/{slug}', 'BlogSource@destroy');

            $r->patch('blog/post/edit/{slug}/image/{uuid}', 'Blog@setFeaturedImage')
                ->middleware('can:edit,App\Models\Blog\BlogPost');
            $r->delete('blog/post/edit/{slug}/image/{uuid}', 'Blog@deleteImage')
                ->middleware('can:edit,App\Models\Blog\BlogPost');

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

            $r->get('media/{media}', 'Media@edit');
            $r->patch('media/{media}', 'Media@update');
            $r->post('media/add', 'Media@add')
                ->middleware('can:edit,App\Models\User');
            $r->post('media/crop', 'Media@crop')
                ->middleware('can:edit,App\Models\User');

            $r->get('dashboard', 'Dashboard@index');

            $r->get('system/events/log', 'System@getLog');
        };
    }
}
<?php namespace App\Http\Routes;

use Illuminate\Routing\Router;

class Admin
{
    public function bind(Router $router)
    {
        $router->group([
            'prefix' => 'admin',
            'namespace' => 'App\Http\Controllers\Admin',
        ],
            function (Router $r) {
                $r->group([
                    'middleware' => ['ajax', 'authenticated_admin'],
                ], call_user_func('static::guestAjaxRoutes'));
                $r->group([
                    'middleware' => ['auth:ajax']
                ], call_user_func('static::authAjaxRoutes'));
                $r->group([
                    'middleware' => ['web'],
                ], call_user_func('static::guestWebRoutes'));
            }
        );
    }

    public static function guestWebRoutes()
    {
        return function (Router $r) {
            $r->get('/test', 'Admin@test');
            $r->get('{path}', 'Admin@index')->where('path', '(.*)');
        };
    }

    public static function guestAjaxRoutes()
    {
        return function (Router $r) {
            $r->get('', 'Admin@index')->name('admin.dashboard');
            $r->post('login', 'Auth\Login@login')->name('admin.login');
            $r->post('register', 'Auth\Register@register')->name('admin.register');
            $r->post('password/email', 'Auth\ForgotPassword@sendResetLinkEmail')->name('admin.password.email-reset');
            $r->post('password/reset', 'Auth\ResetPassword@reset')->name('admin.password.reset');
        };

    }

    public static function authAjaxRoutes()
    {
        return function (Router $r) {
            $r->post('logout', 'Auth\Login@logout')->name('admin.logout');
            $r->patch('settings/profile', 'Settings\Profile@update')->name('admin.settings.profile');
            $r->patch('settings/password', 'Settings\Password@update')->name('admin.settings.password');
        };
    }
}
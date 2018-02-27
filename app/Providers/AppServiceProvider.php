<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Models as I;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        I\Person::class,
        I\User::class,
        I\Group::class,
        I\Permission::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('view')->composer('admin.layouts.default', \App\Composers\Admin::class);

        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->bindings as $interface) {
            $this->app->bind($interface, str_replace('\\Contracts', '\\Providers', $interface));

        }
    }
}

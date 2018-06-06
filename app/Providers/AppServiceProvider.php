<?php

namespace App\Providers;

use App\Contracts\RawQueries;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Models as Contract;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        Contract\Person::class,
        Contract\User::class,
        Contract\Group::class,
        Contract\Permission::class,
        Contract\Media::class
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('view')->composer('admin.layouts.default', \App\Composers\Admin::class);

        if (app()->environment() == 'local') {
            Schema::defaultStringLength(191);
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->bindings as $interface) {
            $this->app->bind($interface, str_replace('\\Contracts\\Models', '\\Support\\Providers', $interface));
        }
        $dbDefaultEngine = ucfirst(config('database.default'));
        $this->app->bind(RawQueries::class, sprintf('\\App\\Support\\Database\\%sRawQueries', $dbDefaultEngine));
    }
}

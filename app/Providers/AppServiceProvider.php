<?php namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (env('APP_OLD_ASS_RDBMS')) {
            Schema::defaultStringLength(191);
        }

        if (env('APP_HTTPS_ON')) {
            \URL::forceScheme('https');
        } else {
            \URL::forceScheme('http');
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

    }
}

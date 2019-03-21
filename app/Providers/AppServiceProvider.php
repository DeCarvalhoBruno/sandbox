<?php namespace App\Providers;

use App\Contracts\Models as Contract;
use App\Contracts\RawQueries;
use App\Support\Providers\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        Contract\Person::class,
        Contract\User::class,
        Contract\Group::class
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->app->make('view')->composer('admin.default', \App\Composers\Admin::class);
        $this->app->make('view')->composer(
            'frontend.site.settings.panes.profile',
            \App\Composers\Frontend\Profile::class
        );
        $this->app->make('view')->composer(
            'frontend.site.settings.panes.*',
            \App\Composers\Frontend\Settings::class
        );
        $this->app->make('view')->composer([
            'frontend.auth.*',
            'frontend.site.*',
            'frontend.errors.*',
            'blog::*'
        ], \App\Composers\Frontend::class);
        $this->app->make('view')->composer([
            'frontend.site.home',
        ], \App\Composers\Frontend\Home::class);

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
        $this->app->bind(
            \CyrildeWit\EloquentViewable\Contracts\View::class,
            View::class
        );

        foreach ($this->bindings as $interface) {
            $this->app->bind($interface, str_replace(
                    '\\Contracts\\Models', '\\Support\\Providers', $interface)
            );
        }
        $dbDefaultEngine = ucfirst(config('database.default'));
        $this->app->bind(
            RawQueries::class,
            sprintf('\\App\\Support\\Database\\%sRawQueries', $dbDefaultEngine)
        );
    }
}

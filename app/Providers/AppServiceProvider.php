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
        Contract\Media::class,
        Contract\File::class,
        Contract\Image::class,
        Contract\Avatar::class,
        Contract\Text::class,
        Contract\BlogCategory::class,
        Contract\BlogTag::class,
        Contract\Blog::class,
        Contract\Email::class,
        Contract\EmailList::class,
        Contract\EmailCampaign::class,
        Contract\EmailSchedule::class,
        Contract\EmailSubscriber::class,
        Contract\EmailUserEvent::class
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->make('view')->composer('admin.default', \App\Composers\Admin::class);
        $this->app->make('view')->composer([
            'frontend.auth.*',
            'frontend.site.*',
            'frontend.errors.*'
        ],
            \App\Composers\Frontend::class);
        $this->app->make('view')->composer(
            'frontend.site.settings.profile',
            \App\Composers\Frontend\Profile::class
        );
        $this->app->make('view')->composer(
            'frontend.site.settings.panes.*',
            \App\Composers\Frontend\Settings::class
        );

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

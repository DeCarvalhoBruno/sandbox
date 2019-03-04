<?php

namespace App\Providers;

use App\Contracts\RawQueries;
use App\Support\Providers\View;
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
        Contract\BlogSource::class,
        Contract\BlogTag::class,
        Contract\Blog::class,
        Contract\Email::class,
        Contract\EmailList::class,
        Contract\EmailCampaign::class,
        Contract\EmailSchedule::class,
        Contract\EmailSubscriber::class,
        Contract\EmailUserEvent::class,
        Contract\SystemEventLog::class,
        Contract\SystemUserSettings::class,
        Contract\System::class,
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
            'frontend.errors.*'
        ], \App\Composers\Frontend::class);
        $this->app->make('view')->composer([
            'frontend.site.home',
        ], \App\Composers\Home::class);

        if (env('APP_OLD_ASS_RDBMS')) {
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
        $this->app->bind(
            \CyrildeWit\EloquentViewable\Contracts\View::class,
            View::class
        );

        foreach ($this->bindings as $interface) {
            $this->app->bind($interface, str_replace('\\Contracts\\Models', '\\Support\\Providers', $interface));
        }
        $dbDefaultEngine = ucfirst(config('database.default'));
        $this->app->bind(RawQueries::class, sprintf('\\App\\Support\\Database\\%sRawQueries', $dbDefaultEngine));
    }
}

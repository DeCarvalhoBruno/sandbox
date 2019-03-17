<?php namespace Naraki\Mail;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Naraki\Mail\Commands\SendTestEmail;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__.'/../config/rss.php' => config_path('mail.php'),
        ], 'mail-naraki');

        $this->app->singleton(Contracts\Listing::class, Providers\Listing::class);
        $this->app->singleton(Contracts\UserEvent::class, Providers\UserEvent::class);
        $this->app->singleton(Contracts\Schedule::class, Providers\Schedule::class);
        $this->app->singleton(Contracts\Subscriber::class, Providers\Subscriber::class);
        $this->app->singleton(Contracts\Campaign::class, Providers\Campaign::class);
        $this->app->singleton(Contracts\Email::class, Providers\Email::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('command.naraki.test_email', function () {
            return new SendTestEmail();
        });

        $this->commands(['command.naraki.test_email']);

    }

}
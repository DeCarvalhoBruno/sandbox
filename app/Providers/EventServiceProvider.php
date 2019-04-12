<?php namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Naraki\Permission\Events\PermissionEntityUpdated;
use Naraki\Permission\UpdatePermissions;
use Naraki\Sentry\Events\PersonSentContactRequest;
use Naraki\Sentry\Events\UserRegistered;
use Naraki\Sentry\Listeners\PersonSentContactRequest as PersonSentContactRequestListener;
use Naraki\Sentry\Listeners\UserLogin;
use Naraki\Sentry\Listeners\UserRegistered as UserRegisteredListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Illuminate\Auth\Events\Login::class => [
            UserLogin::class
        ],
        PermissionEntityUpdated::class => [
            UpdatePermissions::class,
        ],
        UserRegistered::class => [
            UserRegisteredListener::class
        ],
        PersonSentContactRequest::class => [
            PersonSentContactRequestListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}

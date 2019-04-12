<?php

namespace App\Providers;

use Naraki\Permission\Events\PermissionEntityUpdated;
use Naraki\Sentry\Events\PersonSentContactRequest;
use Naraki\Sentry\Events\UserRegistered;
use App\Listeners\PersonSentContactRequest as PersonSentContactRequestListener;
use Naraki\Permission\UpdatePermissions;
use App\Listeners\UserLogin;
use App\Listeners\UserRegistered as UserRegisteredListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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

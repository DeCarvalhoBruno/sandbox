<?php

namespace App\Providers;

use App\Events\PermissionEntityUpdated;
use App\Events\PersonSentContactRequest;
use App\Events\UserRegistered;
use App\Events\UserSubscribedToNewsletter;
use App\Listeners\PersonSentContactRequest as PersonSentContactRequestListener;
use App\Listeners\UpdatePermissions;
use App\Listeners\UserLogin;
use App\Listeners\UserRegistered as UserRegisteredListener;
use App\Listeners\UserSubscribedToNewsletter as UserSubscribedToNewsletterListener;
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
            UpdatePermissions::class,
            UserRegisteredListener::class
        ],
        PersonSentContactRequest::class => [
            PersonSentContactRequestListener::class
        ],
        UserSubscribedToNewsletter::class=>[
            UserSubscribedToNewsletterListener::class
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

<?php

namespace App\Providers;

use App\Events\PermissionEntityUpdated;
use App\Events\UserRegistered;
use App\Listeners\UpdatePermissions;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\UserRegistered as UserRegisteredListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        PermissionEntityUpdated::class => [
            UpdatePermissions::class,
        ],
        UserRegistered::class => [
            UpdatePermissions::class,
            UserRegisteredListener::class
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

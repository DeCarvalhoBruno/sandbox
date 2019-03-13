<?php

namespace App\Providers;

use App\Contracts\Models\User as UserInterface;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\User::class => \App\Policies\User::class,
        \App\Models\Group::class => \App\Policies\Group::class,
        \Naraki\Blog\Models\BlogPost::class => \App\Policies\Group::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userProvider
     * @return void
     */
    public function boot(UserInterface $userProvider)
    {
        \Auth::provider('CustomUserProvider', function () use ($userProvider) {
            return $userProvider;
        });

        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

    }
}

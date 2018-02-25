<?php

namespace App\Providers;

use App\Contracts\Models\User as UserInterface;
use App\Models as Models;
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
        Models\User::class,
        Models\Group::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @param \App\Contracts\Models\User|\App\Providers\Models\User $userProvider
     * @return void
     */
    public function boot(UserInterface $userProvider)
    {
        \Auth::provider('CustomUserProvider', function () use ($userProvider) {
            return $userProvider;
        });

        foreach ($this->policies as $value) {
            Gate::policy($value, str_replace('\\Models', '\\Policies', $value));
        }

        //
    }
}

<?php

namespace App\Providers;

use App\Contracts\Models\User as UserInterface;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
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
        $this->registerPolicies();

        //
    }
}

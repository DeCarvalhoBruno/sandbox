<?php

namespace App\Providers;

use App\Providers\Models\User as UserProvider;
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
     * @return void
     */
    public function boot()
    {
        \Auth::provider('CustomUserProvider', function () {
            return new UserProvider();
        });
        $this->registerPolicies();

        //
    }
}

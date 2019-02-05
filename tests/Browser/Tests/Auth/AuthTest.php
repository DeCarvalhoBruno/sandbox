<?php

namespace Tests\Browser\Tests\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\CreatesDatabaseResources;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    use DatabaseMigrations, CreatesDatabaseResources;

    /**
     * A basic browser test example.
     *
     * @return void
     * @throws \Throwable
     */
    public function test_see_password_reset()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route_i18n('password.request'))
                ->assertPresent('input[type="email"]')
                ->assertPresent('input[type="password"]');

        });
    }

}

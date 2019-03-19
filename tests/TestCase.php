<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, CreatesDatabaseResources;
    protected $oldExceptionHandler;

    protected function setUp(): void
    {
        parent::setUp();
//        \DB::statement('PRAGMA foreign_keys=on;');

        $this->withoutExceptionHandling();
        $this->withoutGate();
    }

    protected function signIn($user = null)
    {
        $this->actingAs($user ?: $this->createUser());
        return $this;
    }

    protected function userIdOnceSignedIn()
    {
        return auth()->user()->id;
    }

    protected function withoutGate()
    {
        \Gate::before(function () {
            return true;
        });
    }

}

<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $oldExceptionHandler;

    protected function setUp()
    {
        parent::setUp();
        \DB::statement('PRAGMA foreign_keys=on;');
        $this->withoutExceptionHandling();
    }

    protected function signIn($user = null)
    {
        $this->actingAs($user ?: $this->createUser());
        return $this;
    }

    protected function userIdOnceSignedIn(){
        return auth()->user()->id;
    }

    protected function create($class, $attributes = [], $times=null)
    {
        return factory('App\\Models\\'.$class,$times)->create($attributes);
    }

    protected function make($class, $attributes = [], $times=null)
    {
        return factory('App\\Models\\' . $class,$times)->make($attributes);
    }

    /**
     * @return \App\Models\User
     */
    protected function createUser()
    {
        $u = $this->create('User');
        $this->create('Person',['user_id'=>$u->user_id]);

        return User::find($u->user_id);
    }

}

<?php

namespace Tests\Feature\Frontend;

use App\Exceptions\EmailTakenException;
use App\Models\OAuthProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laravel\Socialite\Facades\Socialite;
use Mockery as m;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function user_gets_created(){

        $this->assertEquals(2, User::query()->get()->count());

        $abstractUser = m::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(str_random(10))
            ->shouldReceive('getEmail')
            ->andReturn(str_random(10) . '@gmail.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage')
            ->shouldReceive('getNickname')
            ->andReturn(str_random(15));
        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $this->get('/oauth/google/callback');
        $this->assertEquals(3, User::query()->get()->count());
        $this->assertEquals(1, OAuthProvider::query()->get()->count());
    }

    public function user_with_existing_email()
    {
        $abstractUser = m::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(str_random(10))
            ->shouldReceive('getEmail')
            ->andReturn('system@localhost.local')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage')
            ->shouldReceive('getNickname')
            ->andReturn(str_random(15));
        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $this->expectException(EmailTakenException::class);
        $this->get('/oauth/google/callback');
    }

    public function user_with_existing_user()
    {
        $abstractUser = m::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(str_random(10))
            ->shouldReceive('getEmail')
            ->andReturn('system@localhost.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage')
            ->shouldReceive('getNickname')
            ->andReturn('root');
        Socialite::shouldReceive('driver->user')->andReturn($abstractUser);

        $this->expectException(EmailTakenException::class);
        $this->get('/oauth/google/callback');
    }


}
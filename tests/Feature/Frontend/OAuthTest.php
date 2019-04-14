<?php

namespace Tests\Feature\Frontend;

use Naraki\Sentry\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Mockery as m;
use Naraki\Oauth\Models\OAuthProvider;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_oauth_user_gets_created()
    {
        $this->withoutJobs();
        $mockSocialiteUser = m::mock('Laravel\Socialite\Two\User');
        $mockSocialiteUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn('dfsdfefefff')
            ->shouldReceive('getEmail')
            ->andReturn('email@domain.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage')
            ->shouldReceive('getNickname')
            ->andReturn(Str::random(15));
        Socialite::shouldReceive('driver->stateless->user')->andReturn($mockSocialiteUser);

        $this->get('/oauth/google/callback');
        $this->assertEquals(3, $this->cnt(User::class));
        $this->assertEquals(1, $this->cnt(OAuthProvider::class));
        $this->assertTrue(auth()->check());
        $this->assertEquals('email@domain.com', auth()->user()->getAttribute('email'));
    }

    public function test_oauth_user_with_existing_email()
    {
        $this->withoutJobs();
        $mockSocialiteUser = m::mock('Laravel\Socialite\Two\User');
        $mockSocialiteUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(Str::random(10))
            ->shouldReceive('getEmail')
            ->andReturn('system@localhost.local')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage')
            ->shouldReceive('getNickname')
            ->andReturn(Str::random(15));
        Socialite::shouldReceive('driver->stateless->user')->andReturn($mockSocialiteUser);

        $this->get('/oauth/google/callback');
        $this->assertEquals(2, $this->cnt(User::class));
        $this->assertEquals(1, $this->cnt(OAuthProvider::class));
        $this->assertTrue(auth()->check());
        $this->assertEquals('system@localhost.local', auth()->user()->getAttribute('email'));
    }

    public function test_oauth_user_with_existing_oauth()
    {
        $this->withoutJobs();
        $oauthUserId = rand();
        $accessToken = Str::random(40);
        $refreshToken = Str::random(40);
        $u = $this->createUser();
        $this->assertEquals(3, $this->cnt(User::class));
        OAuthProvider::query()->create([
            'user_id' => $u->getKey(),
            'provider' => 'google',
            'provider_user_id' => $oauthUserId,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ]);

        $mockSocialiteUser = m::mock('Laravel\Socialite\Two\User');
        $mockSocialiteUser
            ->shouldReceive('getId')
            ->andReturn($oauthUserId)
            ->shouldReceive('getName')
            ->andReturn($u->getAttribute('full_name'))
            ->shouldReceive('getEmail')
            ->andReturn($u->getAttribute('email'))
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage')
            ->shouldReceive('getNickname')
            ->andReturn($u->getAttribute('username'));

        Socialite::shouldReceive('driver->stateless->user')->andReturn($mockSocialiteUser);

        $oauthProvider = OAuthProvider::query()->first();
        $this->assertEquals($accessToken, $oauthProvider->getAttribute('access_token'));

        $this->get('/oauth/google/callback');
        $oauthProvider = OAuthProvider::query()->first();
        $this->assertEquals(null, $oauthProvider->getAttribute('access_token'));
        $this->assertEquals(1, $this->cnt(OAuthProvider::class));
        $this->assertEquals(3, $this->cnt(User::class));
        $this->assertTrue(auth()->check());
    }

    public function test_oauth_yolo()
    {
        $this->withoutJobs();
        m::mock('overload:' . \Google_Client::class)
            ->shouldReceive('verifyIdToken')
            ->with('ciOiJSUzI1NiIsImtpZCI6IjA5MDVkNmY5Y2Q5')
            ->andReturn([
                'iss' => 'https=>//accounts.google.com',
                'nbf' => 1553714603,
                'aud' => '131076844713-grb0vaq86p6e35r846t8a3ma75u96ugl.apps.googleusercontent.com',
                'sub' => '100771804164370744357',
                'email' => 'user_email@gmail.com',
                'email_verified' => true,
                'azp' => '131076844713-grb0vaq86p6e35r846t8a3ma75u96ugl.apps.googleusercontent.com',
                'name' => 'First Name And Last Name',
                'picture' => 'https=>//lh6.googleusercontent.com/DEFHEesdgQ/s96-c/photo.jpg',
                'given_name' => 'User First Name',
                'family_name' => 'User Last Name',
                'iat' => 1553714903,
                'exp' => 1553718503,
                'jti' => 'ff32ffb749f70cdf420a9582fff7d463b371c960'
            ]);
        $this->post('/oauth-yolo', [
            'google_token' => 'ciOiJSUzI1NiIsImtpZCI6IjA5MDVkNmY5Y2Q5',
            'avatar'=>'avatar'
        ]);
        $this->assertTrue(auth()->check());
        $this->assertEquals('user_email@gmail.com', auth()->user()->getAttribute('email'));

    }


}
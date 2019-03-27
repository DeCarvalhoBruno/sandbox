<?php

namespace Tests\Feature\Frontend;

use App\Models\OAuthProvider;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Laravel\Socialite\Facades\Socialite;
use Mockery as m;
use Tests\TestCase;

class OAuthTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_oauth_user_gets_created()
    {

        $this->assertEquals(2, User::query()->get()->count());

        $mockSocialiteUser = m::mock('Laravel\Socialite\Two\User');
        $mockSocialiteUser
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
        Socialite::shouldReceive('driver->stateless->user')->andReturn($mockSocialiteUser);

        $this->get('/oauth/google/callback');
        $this->assertEquals(3, User::query()->get()->count());
        $this->assertEquals(1, OAuthProvider::query()->get()->count());
    }

    public function test_oauth_user_with_existing_email()
    {
        $mockSocialiteUser = m::mock('Laravel\Socialite\Two\User');
        $mockSocialiteUser
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
        Socialite::shouldReceive('driver->stateless->user')->andReturn($mockSocialiteUser);

        $this->get('/oauth/google/callback');
        $this->assertEquals(2, User::query()->get()->count());
        $this->assertEquals(1, OAuthProvider::query()->get()->count());
    }

    public function test_oauth_yolo()
    {


//        $mockVerifyToken=m::mock(\Google_AccessToken_Verify::class);
        $mockGoogle = m::mock('overload:'.\Google_Client::class)
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
            'google_token' => 'ciOiJSUzI1NiIsImtpZCI6IjA5MDVkNmY5Y2Q5'
        ]);

        /*
         * /*
        "iss":"https://accounts.google.com",
        "nbf":1553714603,
        "aud":"131076844713-grb0vaq86p6e35r846t8a3ma75u96ugl.apps.googleusercontent.com",
        "sub":"100771804164370744357",
        "email":"decarvalho.bruno.en@gmail.com",
        "email_verified":true,
        "azp":"131076844713-grb0vaq86p6e35r846t8a3ma75u96ugl.apps.googleusercontent.com",
        "name":"Bruno De Carvalho",
        "picture":"https://lh6.googleusercontent.com/-dS1j5mg4364/AAAAAAAAAAI/AAAAAAAAABM/RFMQoA2VIPY/s96-c/photo.jpg",
        "given_name":"Bruno",
        "family_name":"De Carvalho",
        "iat":1553714903,
        "exp":1553718503,
        "jti":"ff32ffb749f70cdf420a9582fff7d463b371c960"
        */


    }


}
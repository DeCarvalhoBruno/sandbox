<?php

namespace Tests\Feature\Frontend;

use Naraki\Sentry\Models\Person;
use Naraki\Sentry\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_user_registration_without_required()
    {
        $this->withExceptionHandling();
        $this->withoutJobs();
        $response = $this->post('register', [
            'first_name' => 'First name',
        ]);
        $sessionErrors = $response->baseResponse->getSession()->get('errors')->getMessages();
        $this->assertArrayHasKey('username', $sessionErrors);
        $this->assertArrayHasKey('email', $sessionErrors);
        $this->assertArrayHasKey('password', $sessionErrors);
    }

    public function test_user_registration_normal()
    {
        $this->withoutJobs();
        $this->assertEquals(2, Person::query()->select(['user_id'])->count());
        $this->assertEquals(2, User::query()->select(['user_id'])->count());
        $response = $this->post('register', [
            'first_name' => 'First name',
            'username' => 'brian_campbell',
            'email' => 'brian.campbell@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $this->assertEquals(3, Person::query()->select(['user_id'])->count());
        $this->assertEquals(3, User::query()->select(['user_id'])->count());
        $response->assertRedirect(route('login'));
    }

    public function test_user_registration_with_existing_person()
    {
        $this->withoutJobs();
        Person::query()->create(['email' => 'brian.campbell@example.com']);
        $this->assertEquals(3, Person::query()->select(['user_id'])->count());
        $this->assertEquals(2, (new User)->newQueryWithoutScopes()->select(['user_id'])->count());
        $this->assertEquals(0, intval(Person::query()->select(['user_id'])
            ->where('email', 'brian.campbell@example.com')->first()->getAttribute('user_id')));
        $response = $this->post('register', [
            'first_name' => 'First name',
            'username' => 'brian_campbell',
            'email' => 'brian.campbell@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $response->assertRedirect(route('login'));
        $this->assertEquals(3, Person::query()->select(['user_id'])->count());
        $this->assertEquals(3, User::query()->select(['user_id'])->count());

        $this->assertNotEquals(0, intval(Person::query()->select(['user_id'])
            ->where('email', 'brian.campbell@example.com')->first()->getAttribute('user_id')));
    }

    public function test_user_registration_with_existing_user()
    {
        $this->withoutJobs();
        $this->withExceptionHandling();
        $u = $this->createUser();

        $response = $this->post('register', [
            'username' => 'brian_campbell',
            'email' => $u->getAttribute('email'),
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $sessionErrors = $response->baseResponse->getSession()->get('errors')->getMessages();
        $this->assertArrayHasKey('email', $sessionErrors);

    }

}
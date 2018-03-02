<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function test_show()
    {
        $this->signIn();
        $user = $this->createUser();

//        $mock = \Mockery::mock('Illuminate\Contracts\Auth\Access\Gate');
//        $mock->shouldReceive('authorize')->with('can:view,App\Models\User')->once()->andReturn(true);
//        $this->app->instance('Illuminate\Contracts\Auth\Access\Gate', $mock);

        $response = $this->getJson('/ajax/admin/users/' . $user->username);

        $response->assertStatus(200);
        $this->assertArraySubset(['first_name', 'last_name', 'email', 'username'], array_keys($response->json()));
    }

    public function test_without_authentication()
    {
        $user = $this->createUser();

        $this->getJson('/ajax/admin/users/' . $user->username)->assertStatus(401);

        $this->patchJson("/ajax/admin/users/{$user->username}")->assertStatus(401);
    }

    public function test_update_normal()
    {
        $user = $this->signIn()->createUser();
        $username = 'b_wagner';
        $this->patchJson("/ajax/admin/users/{$user->username}",
            ['first_name' => 'Bobby', 'last_name' => 'Wagner', 'username' => 'b_wagner','email'=>'user@example.com'])->assertStatus(204);

        $changedUser = User::query()->where('username',$username)->first();
        $this->assertNotEquals($user->first_name . ' ' . $user->last_name,
            $changedUser->first_name . ' ' . $changedUser->last_name);
        $this->assertNotEquals($user->username, $changedUser->username);
        $this->assertNotEquals($user->email, $changedUser->email);

    }

    public function test_update_without_valid_email()
    {
        $response = $this->validation_testing_setup(['new_email' => 'fdhj@f']);
        $json = $response->json();
        $this->assertArrayHasKey('new_email', $json['errors']);
    }

    public function test_update_without_valid_username()
    {
        $response = $this->validation_testing_setup(['new_username' => 'fdhj@f']);
        $json = $response->json();
        $this->assertArrayHasKey('new_username', $json['errors']);
    }

    private function validation_testing_setup($formParams)
    {
        $this->withExceptionHandling();
        $user = $this->signIn()->createUser();

        $response = $this->patchJson(
            "/ajax/admin/users/{$user->username}",$formParams
        );

        $response->assertStatus(422);
        return $response;
    }
}
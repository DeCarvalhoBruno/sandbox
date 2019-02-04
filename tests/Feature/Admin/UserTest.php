<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_show()
    {
        $this->signIn();
        $user = $this->createUser();

        $response = $this->getJson('/ajax/admin/users/' . $user->username);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals(['first_name', 'last_name', 'email', 'username','full_name'], array_keys($json['user']));
    }

    public function test_update_normal()
    {
        $user = $this->signIn()->createUser();
        $username = 'b_wagner';
        $this->patchJson("/ajax/admin/users/{$user->username}",
            [
                'first_name' => 'Bobby',
                'last_name' => 'Wagner',
                'new_username' => 'b_wagner',
                'new_email' => 'user@example.com',
                'permissions' => []
            ])->assertStatus(204);

        $changedUser = User::query()->where('username', $username)->first();
        $this->assertNotEquals($user->first_name . ' ' . $user->last_name,
            $changedUser->first_name . ' ' . $changedUser->last_name);
        $this->assertNotEquals($user->username, $changedUser->username);
        $this->assertNotEquals($user->email, $changedUser->email);

    }

    public function test_update_without_valid_email()
    {
        $response = $this->validation_testing_setup(['new_email' => 'fdhj@f','permissions'=>[]]);
        $json = $response->json();
        $this->assertArrayHasKey('new_email', $json['errors']);
    }

    public function test_update_without_valid_username()
    {
        $response = $this->validation_testing_setup(['new_username' => 'fdhj@f','permissions'=>[]]);
        $json = $response->json();
        $this->assertArrayHasKey('new_username', $json['errors']);
    }

    public function test_delete_one_user()
    {
        $user = $this->signIn()->createUser();
        $this->delete(
            "/ajax/admin/users/{$user->username}"
        )->assertStatus(204);
        
    }

    public function test_delete_batch_users()
    {
        $user = $this->signIn()->createUser(3);
        $this->assertCount(
            6,
            \App\Models\User::all());
        $users =array_map(function($v){return $v->username;},$user);

        $this->postJson(
            "/ajax/admin/users/batch/delete",['users'=>$users]
        )->assertStatus(204);
        $this->assertCount(
            3,
            \App\Models\User::all());

    }

    private function validation_testing_setup($formParams)
    {
        $this->withExceptionHandling();
        $user = $this->signIn()->createUser();

        $response = $this->patchJson(
            "/ajax/admin/users/{$user->username}", $formParams
        );

        $response->assertStatus(422);
        return $response;
    }
    
}
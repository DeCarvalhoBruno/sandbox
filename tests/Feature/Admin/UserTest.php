<?php

namespace Tests\Feature\Admin;

use App\Contracts\Models\User as UserProvider;
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
        $response = $this->getJson('/ajax/admin/user/' . $user->user_id);

        $response->assertStatus(200);
        $this->assertArraySubset(['first_name', 'last_name', 'email', 'username'], array_keys($response->json()));
    }

    public function test_without_authentication()
    {
        $user = $this->createUser();

        $this->getJson('/ajax/admin/user/' . $user->user_id)->assertStatus(401);

        $this->patchJson("/ajax/admin/user/{$user->user_id}/update")->assertStatus(401);
    }

    public function test_update_normal()
    {
        $user = $this->signIn()->createUser();
        $this->patchJson("/ajax/admin/user/{$user->user_id}/update",
            ['first_name' => 'Bobby', 'last_name' => 'Wagner', 'username' => 'b_wagner','email'=>'user@example.com'])->assertStatus(204);

        $changedUser = User::find($user->user_id);
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
            "/ajax/admin/user/{$user->user_id}/update",$formParams
        );

        $response->assertStatus(422);
        return $response;

    }
}
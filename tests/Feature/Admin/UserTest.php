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
            ['first_name' => 'Bobby', 'last_name' => 'Wagner', 'username' => 'b_wagner'])->assertStatus(200);

        $changedUser = User::find($user->user_id);
        $this->assertNotEquals($user->first_name.' ' .$user->last_name,$changedUser->first_name.' ' .$changedUser->last_name);
        $this->assertNotEquals($user->username,$changedUser->username);

    }

    public function test_update_without_required_params()
    {
        $user = $this->signIn()->createUser();

        $this->patchJson("/ajax/admin/user/{$user->user_id}/update",
            ['first_name' => 'Bobby', 'last_name' => 'Wagner', 'username' => 'b_wagner','email'=>'fdhj@f']);
    }
}
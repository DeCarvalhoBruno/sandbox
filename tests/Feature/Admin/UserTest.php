<?php

namespace Tests\Feature\Admin;

use App\Models\Group;
use App\Models\GroupMember;
use Naraki\Sentry\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function show()
    {
        $user = $this->signIn()->createUser();

        $response = $this->getJson('/ajax/admin/users/' . $user->username);

        $response->assertStatus(200);
        $json = $response->json();
        $this->assertEquals(['first_name', 'last_name', 'email', 'username', 'full_name'], array_keys($json['user']));
    }

    public function test_backend_users_update_normal()
    {
        $user = $this->signIn()->createUser();
        $username = 'b_wagner';
        $this->patchJson("/ajax/admin/users/{$user->username}",
            [
                'first_name' => 'Bobby',
                'last_name' => 'Wagner',
                'username' => 'b_wagner',
                'email' => 'user@example.com',
                'permissions' => []
            ])->assertStatus(204);

        $changedUser = User::query()->where('username', $username)->first();
        $this->assertNotEquals($user->first_name . ' ' . $user->last_name,
            $changedUser->first_name . ' ' . $changedUser->last_name);
        $this->assertNotEquals($user->username, $changedUser->username);
        $this->assertNotEquals($user->email, $changedUser->email);

    }

    public function test_backend_users_update_without_valid_email()
    {
        $response = $this->validation_testing_setup(['email' => 'fdhjfcd.z', 'permissions' => []]);
        $json = $response->json();
        $this->assertArrayHasKey('email', $json['errors']);
    }

    public function test_backend_users_update_without_valid_username()
    {
        $response = $this->validation_testing_setup(['username' => 'fdhj@f', 'permissions' => []]);
        $json = $response->json();
        $this->assertArrayHasKey('username', $json['errors']);
    }

    public function test_backend_users_delete_one_user()
    {
        $user = $this->signIn()->createUser();
        $this->delete(
            "/ajax/admin/users/{$user->username}"
        )->assertStatus(204);
    }

    public function test_backend_users_delete_batch_users()
    {
        $user = $this->signIn()->createUser(3);
        $this->assertCount(
            6,
            \Naraki\Sentry\Models\User::all());
        $users = array_map(function ($v) {
            return $v->username;
        }, $user);

        $this->postJson(
            "/ajax/admin/users/batch/delete", ['users' => $users]
        )->assertStatus(204);
        $this->assertCount(
            3,
            \Naraki\Sentry\Models\User::all());

    }

    public function test_backend_users_create()
    {
        $group = $this->create('Group');
        $group2 = $this->create('Group');

        $this->assertEquals(1, GroupMember::query()->select(['group_id'])->count());

        $response = $this->postJson(
            '/ajax/admin/user/create',
            [
                'username' => 'sdjfklsdjflkjf',
                'email' => 'dklfjlj@email.com',
                'password' => 'dskfjlj;',
                'groups' => [$group->getAttribute('group_slug'), $group2->getAttribute('group_slug')]
            ]
        );

        $response->assertStatus(204);
        $this->assertEquals(3, GroupMember::query()->select(['group_id'])->count());
    }

    public function test_backend_users_update_with_groups()
    {
        $group = $this->create('Group');
        $group2 = $this->create('Group');
        $group3 = $this->create('Group');

        $this->postJson(
            '/ajax/admin/user/create',
            [
                'username' => 'asddsd',
                'email' => 'asddsd@email.com',
                'password' => 'dskfjlj;',
                'groups' => [$group->getAttribute('group_slug'), $group2->getAttribute('group_slug')]
            ]
        );
        $userId = User::query()->select(['users.user_id'])
            ->where('username', 'asddsd')->value('user_id');

        $this->assertEquals(
            [$group->getKey(), $group2->getKey()],
            GroupMember::query()->select(['group_id'])
                ->where('user_id', $userId)->pluck('group_id')->toArray()
        );

        $this->patchJson(
            "/ajax/admin/users/asddsd",
            ['groups' => [$group2->getAttribute('group_slug'), $group3->getAttribute('group_slug')]]
        );
        $this->assertEquals(
            [$group2->getKey(), $group3->getKey()],
            GroupMember::query()->select(['group_id'])
                ->where('user_id', $userId)->pluck('group_id')->toArray()
        );

        $this->patchJson(
            "/ajax/admin/users/asddsd",
            ['groups' => []]
        );
        $this->assertEquals(
            [],
            GroupMember::query()->select(['group_id'])
                ->where('user_id', $userId)->pluck('group_id')->toArray()
        );
    }

    private function validation_testing_setup($formParams)
    {
        $this->withExceptionHandling();
        $user = $this->createUser();
        $this->signIn($user, 'jwt');

        $response = $this->patchJson(
            "/ajax/admin/users/{$user->username}", $formParams
        );

        $response->assertStatus(422);
        return $response;
    }

}
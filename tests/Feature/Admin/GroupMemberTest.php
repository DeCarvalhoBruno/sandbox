<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class GroupMemberTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_insertion()
    {
        $this->signIn();
        $users = $this->createUser(3);
        $group = $this->create('Group');
        $this->assignUserToGroup($users, $group);

        $user = $this->createUser();
        $data = ['added' => [['id' => $user->username]]];

        $this->assertCount(
            3,
            \App\Models\GroupMember::query()->where('group_id', $group->group_id)->get());

        $response = $this->patchJson("/ajax/admin/members/{$group->group_name}", $data);

        $this->assertCount(
            4,
            \App\Models\GroupMember::query()->where('group_id', $group->group_id)->get());
        $response->assertStatus(204);
    }

    public function test_deletion()
    {
        $this->signIn();
        $users = $this->createUser(3);
        $group = $this->create('Group');
        $this->assignUserToGroup($users, $group);

        $data = ['removed' => [['id' => $users[2]->username]]];

        $this->assertCount(
            3,
            \App\Models\GroupMember::query()->where('group_id', $group->group_id)->get());

        $response = $this->patchJson(
            "/ajax/admin/members/{$group->group_name}",
            $data
        );

        $this->assertCount(
            2,
            \App\Models\GroupMember::query()->where('group_id', $group->group_id)->get());
        $response->assertStatus(204);
    }
}
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use DatabaseMigrations;

    public function test_show()
    {
        $this->signIn();
        $user = $this->createUser();
        $group = $this->create('Group');
        $this->assignUserToGroup($user, $group);

        $response = $this->getJson('/ajax/admin/groups/' . $group->group_name);

        $response->assertStatus(200);
        $this->assertArraySubset(['group_name', 'group_mask'], array_keys($response->json()));
    }

    public function test_update_without_data()
    {
        $this->withExceptionHandling();
        $this->signIn()->createUser();
        $group = $this->create('Group');
        $response = $this->patchJson(
            "/ajax/admin/groups/{$group->group_name}",['group_mask' => 'a']
        );
        $response->assertStatus(422);
        $json = $response->json();
        $this->assertArrayHasKey('group_mask', $json['errors']);
        $this->assertArrayHasKey('group_name', $json['errors']);
    }
}
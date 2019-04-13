<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_backend_group_show()
    {
        $this->signIn();
        $user = $this->createUser();
        $group = $this->createGroup();
        $this->assignUserToGroup($user, $group);

        $response = $this->getJson('/ajax/admin/groups/' . $group->group_slug);

        $response->assertStatus(200);
        $jsonResponse = $response->json();
        $this->assertArrayHasKey('group_name', $jsonResponse['group']);
        $this->assertArrayHasKey('group_mask', $jsonResponse['group']);
        $this->assertArrayHasKey('permissions', $jsonResponse);
    }

    public function test_backend_group_update_without_data()
    {
        $this->withExceptionHandling();
        $this->signIn($this->createUser());
        $group = $this->createGroup();
        $response = $this->patchJson(
            "/ajax/admin/groups/{$group->group_name}", ['group_mask' => 'a', 'permissions' => []]
        );
        $response->assertStatus(422);
        $json = $response->json();

        $this->assertArrayHasKey('group_mask', $json['errors']);
    }

    public function test_backend_group_update_with_bad_name()
    {
        $this->withExceptionHandling();
        $this->signIn($this->createUser());
        $group = $this->createGroup();
        $response = $this->patchJson(
            "/ajax/admin/groups/{$group->group_name}", ['new_group_name' => 'root', 'permissions' => []]
        );
        $response->assertStatus(422);
        $json = $response->json();
        $this->assertArrayHasKey('new_group_name', $json['errors']);
    }
}
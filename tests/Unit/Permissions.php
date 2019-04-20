<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Naraki\Core\Models\Entity;
use Naraki\Core\Models\EntityType;
use Naraki\Permission\Facades\Permission as PermissionProvider;
use Naraki\Permission\Models\PermissionStore;
use Naraki\Permission\Support\Permission;
use Naraki\Sentry\Models\Group;
use Naraki\Sentry\Models\GroupMember;
use Tests\TestCase;

class Permissions extends TestCase
{
    use DatabaseMigrations, WithoutMiddleware;

    public function test_permissions_root()
    {
        Permission::assignToAll();
        $rootPermissions = PermissionProvider::getAllUserPermissions(EntityType::ROOT_ENTITY_TYPE_ID);

        $this->assertEquals(
            array_keys($rootPermissions->computed),
            ['blog_posts', 'groups', 'media', 'system', 'users']
        );
        $this->assertEquals(array_keys($rootPermissions->computed['blog_posts']), ['View', 'Add', 'Edit', 'Delete']);
        $this->assertEquals(array_keys($rootPermissions->computed['groups']), ['View', 'Add', 'Edit', 'Delete']);
        $this->assertEquals(array_keys($rootPermissions->computed['media']), ['View', 'Add', 'Edit', 'Delete']);
        $this->assertEquals(array_keys($rootPermissions->computed['system']),
            ['Login', 'Settings', 'Permissions', 'Notifications']);
        $this->assertEquals(array_keys($rootPermissions->computed['users']), ['View', 'Add', 'Edit', 'Delete']);

    }

    /**
     * Regular users don't have permissions on anything
     */
    public function test_permissions_regular_user()
    {
        $user = $this->createUser();
        $this->assignUserToGroup($user, $this->createGroup());
        Permission::assignToAll();
        $permissions = PermissionProvider::getAllUserPermissions(
            EntityType::getEntityTypeID(Entity::USERS, $user->getKey())
        );
        $this->assertEmpty($permissions->computed);
    }

    public function test_permissions_specific_user_without_group()
    {
        $this->withoutEvents();
        $user = $this->createUser();
        $entity = EntityType::getEntityTypeID(Entity::USERS, $user->getKey());
        $this->patchJson("/ajax/admin/users/{$user->username}",
            [
                'permissions' => [
                    'hasChanged' => 'true',
                    'blog_posts' => [
                        'mask' => 15,
                        'hasChanged' => 'true'
                    ],
                    'groups' => [
                        'mask' => 1,
                        'hasChanged' => 'true'
                    ],
                    'media' => [
                        'mask' => 15,
                        'hasChanged' => 'true'
                    ],
                    'system' => [
                        'mask' => 9,
                        'hasChanged' => 'true'
                    ],
                    'users' => [
                        'mask' => 1,
                        'hasChanged' => 'true'
                    ]
                ]
            ]
        );
        Permission::assignToAll();
        $permissions = PermissionProvider::getAllUserPermissions($entity);
        $this->assertEquals(array_keys($permissions->computed['blog_posts']), ['View', 'Add', 'Edit', 'Delete']);
        $this->assertEquals(array_keys($permissions->computed['groups']), ['View']);
        $this->assertEquals(array_keys($permissions->computed['media']), ['View', 'Add', 'Edit', 'Delete']);
        $this->assertEquals(array_keys($permissions->computed['system']), ['Login', 'Notifications']);
        $this->assertEquals(array_keys($permissions->computed['users']), ['View']);
    }


    public function test_permissions_specific_user_with_group()
    {
        $this->withoutEvents();
        $user = $this->createUser();

        $this->postJson(
            "/ajax/admin/groups", [
                'group_mask' => 1000,
                'group_name' => 'group',
                'permissions' => [
                    'hasChanged' => 'true',
                    'blog_posts' => [
                        'mask' => 1,
                        'hasChanged' => 'true'
                    ],
                    'groups' => [
                        'mask' => 4,
                        'hasChanged' => 'true'
                    ],
                    'media' => [
                        'mask' => 8,
                        'hasChanged' => 'true'
                    ],
                    'system' => [
                        'mask' => 8,
                        'hasChanged' => 'true'
                    ],
                    'users' => [
                        'mask' => 12,
                        'hasChanged' => 'true'
                    ]
                ]
            ]
        );
        $this->assignUserToGroup(
            $user,
            Group::query()
            ->where('group_name','group')
            ->select('group_id')->first()
        );

        Permission::assignToAll();
        $permissions = PermissionProvider::getAllUserPermissions(
            EntityType::getEntityTypeID(Entity::USERS, $user->getKey())
        );
        $this->assertEquals(array_keys($permissions->computed['blog_posts']), ['View']);
        $this->assertEquals(array_keys($permissions->computed['groups']), ['Edit']);
        $this->assertEquals(array_keys($permissions->computed['media']), ['Delete']);
        $this->assertEquals(array_keys($permissions->computed['system']), ['Notifications']);
        $this->assertEquals(array_keys($permissions->computed['users']), ['Edit','Delete']);

    }

}

<?php namespace App\Support\Permissions;

use App\Models\Entity;
use App\Models\PermissionMask;
use App\Models\PermissionRecord;
use App\Models\PermissionStore;

class Group extends Permission
{
    public function __construct()
    {
        parent::__construct(\App\Models\Group::class, Entity::GROUPS);
    }

    public function assignPermissions()
    {
        $this->populateDb($this->getUsersWithPermissions());
    }

    /**
     * @param \stdClass[] $usersWithPermissions
     */
    protected function populateDb($usersWithPermissions)
    {
        $groups = $this->sqlGetGroups();
        $users = $this->sqlGetUsersGroups(array_keys($usersWithPermissions));
        $userInfo = [];
        foreach ($users as $user) {
            //We're only keeping the user's highest group in the hierarchy, the group with the lowest group_mask.
            if (!isset($userInfo[$user->user_id]) || (isset($userInfo[$user->user_id]) && $user->group_mask < $userInfo[$user->user_id]->group_mask)) {
                $userInfo[$user->user_id] = (object)$user->toArray();
            }
        }
        unset($users);

        $permissionMasks = $permissionRecords = [];
        foreach ($usersWithPermissions as $userWithPermission) {
            $permissionStoreId = (new PermissionStore())->insertGetId([]);
            $permissionMasks[] = [
                'permission_store_id' => $permissionStoreId,
                'permission_holder_id' => $userInfo[$userWithPermission->user_id]->entity_type_id,
                'permission_mask' => $userWithPermission->permission_mask,
                'permission_is_default' => true
            ];
            $permissionRecords[] = [
                'entity_id' => $this->entityId,
                'permission_target_id' => 0,
                'permission_store_id' => $permissionStoreId
            ];
        }
        foreach ($groups as $group) {
            foreach ($usersWithPermissions as $userWithPermission) {
                if ($userInfo[$userWithPermission->user_id]->group_mask < $group->getAttribute('group_mask')) {
                    $permissionStoreId = (new PermissionStore())->insertGetId([]);
                    $permissionMasks[] = [
                        'permission_store_id' => $permissionStoreId,
                        'permission_holder_id' => $userInfo[$userWithPermission->user_id]->entity_type_id,
                        'permission_mask' => $userWithPermission->permission_mask,
                        'permission_is_default' => false
                    ];
                    $permissionRecords[] = [
                        'entity_id' => $this->entityId,
                        'permission_target_id' => $group->getAttribute('entity_type_id'),
                        'permission_store_id' => $permissionStoreId
                    ];
                }
            }
        }
        (new PermissionRecord)->insert($permissionRecords);
        (new PermissionMask)->insert($permissionMasks);
    }

    private function sqlGetGroups()
    {
        return \App\Models\Group::query()->select(['entity_type_id', 'group_mask'])->entityType()->get();
    }

}
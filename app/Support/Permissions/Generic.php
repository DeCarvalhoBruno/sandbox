<?php namespace App\Support\Permissions;

use App\Models\PermissionMask;
use App\Models\PermissionRecord;
use App\Models\PermissionStore;

class Generic extends Permission
{
    public function __construct($modelClass, $entityId)
    {
        parent::__construct($modelClass, $entityId);
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
        $users = $this->sqlGetUsersGroups(array_keys($usersWithPermissions));
        $userInfo = [];
        foreach ($users as $user) {
            //We're only keeping the user's highest group in the hierarchy, the group with the lowest group_mask.
            if (!isset($userInfo[$user->user_id])) {
                $userInfo[$user->user_id] = [(object)$user->toArray()];
            }else{
                $userInfo[$user->user_id][] = (object)$user->toArray();
            }
        }
//        dd($users->toArray(),$usersWithPermissions,$userInfo);
        dd($usersWithPermissions);
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
        (new PermissionRecord)->insert($permissionRecords);
        (new PermissionMask)->insert($permissionMasks);

    }

    private function sqlGetGroups()
    {
        return \App\Models\Group::query()->select(['entity_type_id', 'group_mask'])->entityType()->get();
    }

}
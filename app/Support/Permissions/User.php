<?php namespace App\Support\Permissions;

use App\Models\Entity;

class User extends Permission
{

    private $noPermissionsBitmask = 0;
    private $fullPermissionsBitmask;
    private $permissionsToInsert;

    public function getPermissionsToInsert()
    {
        return $this->permissionsToInsert;
    }

    public function __construct()
    {
        $this->fullPermissionsBitmask = array_sum(\App\Models\User::getConstants('PERMISSION'));
        $this->permissionsToInsert = $this->prepareAllPermissionsForDb(
            $this->arrangeUsersByUserID(),
            $this->getUsersWithPermissions()
        );
    }

    private function sqlUsersWithPermissions()
    {
        return \App\Models\Permission::query()->select([
            'group_members.user_id as group_user_id',
            'users.user_id',
            'permission_mask',
            'entities.entity_id'
        ])->entityType()->entityUser()->leftGroupMember()->leftUser()->orderBy('group_user_id')->orderBy('users.user_id')->get();
    }

    private function sqlGetUsersGroups()
    {
        return \DB::select(
            'SELECT et.entity_type_id,u.user_id, groups.group_mask FROM group_members gu
                  JOIN groups ON gu.group_id = groups.group_id
                  JOIN users u ON gu.user_id = u.user_id
                  JOIN entity_types et ON (et.entity_type_target_id=gu.user_id AND et.entity_id=3100)'
        );
    }

    public function getUsersWithPermissions()
    {
        $dbEntries = $this->sqlUsersWithPermissions();
        $usersWithPermissions = $tmp = [];
        foreach ($dbEntries as $dbEntry) {
            if (!is_null($dbEntry->group_user_id) && (!isset($tmp[$dbEntry->group_user_id]))) {
                $dbEntry->user_id = $dbEntry->group_user_id;
                $usersWithPermissions[$dbEntry->group_user_id] = $dbEntry;
            } elseif (!is_null($dbEntry->user_id)) {
                $tmp[$dbEntry->user_id] = $dbEntry;
                $usersWithPermissions[$dbEntry->user_id] = $dbEntry;
            }
        }
        return $usersWithPermissions;

    }

    public function prepareAllPermissionsForDb($arrangedList, $usersWithPermissions)
    {
        $defaultPermissions = null;
        $permissionsToInsert = [];
        foreach ($arrangedList as $user) {
            $settledToDefaultPermissions = true;
            $tmp = [];
            foreach ($usersWithPermissions as $userWithPermission) {
                if ($user->group_mask < $arrangedList[$userWithPermission->user_id]->group_mask) {
                    $tmp[] = new PermissionDataObj(
                        $user->entity_type_id,
                        $arrangedList[$userWithPermission->user_id]->entity_type_id,
                        $this->noPermissionsBitmask
                    );
                    $settledToDefaultPermissions = false;
                } elseif ($user->user_id == $userWithPermission->user_id) {
                    $tmp[] = new PermissionDataObj(
                        $user->entity_type_id,
                        $arrangedList[$userWithPermission->user_id]->entity_type_id,
                        $this->fullPermissionsBitmask
                    );
                    $settledToDefaultPermissions = false;
                } else {
                    $tmp[] = new PermissionDataObj(
                        $user->entity_type_id,
                        $arrangedList[$userWithPermission->user_id]->entity_type_id,
                        $userWithPermission->permission_mask
                    );
                }
            }
            if ($settledToDefaultPermissions) {
                if (is_null($defaultPermissions)) {
                    $defaultPermissions = new PermissionStoreDataObj(
                        PermissionStoreDataObj::DEFAULT,
                        $tmp);
                }
            } else {
                $permissionsToInsert[] = $tmp;
            }
        }
        return (object)[
            $defaultPermissions,
            new PermissionStoreDataObj(PermissionStoreDataObj::COMPUTED, array_flatten($permissionsToInsert,1))
        ];
    }

    private function arrangeUsersByUserID()
    {
        $users = $this->sqlGetUsersGroups();
        $list = [];
        foreach ($users as $user) {
            //We're only keeping the user's highest group in the hierarchy, the group with the lowest group_mask.
            if (!isset($list[$user->user_id]) || (isset($list[$user->user_id]) && $user->group_mask < $list[$user->user_id]->group_mask)) {
                $list[$user->user_id] = $user;
            }
        }
        return $list;
    }

}

class PermissionStoreDataObj
{
    const DEFAULT = 1;
    const COMPUTED = 2;
    protected $permissions;

    private $type;

    /**
     *
     * @param $type
     * @param $permissions
     */
    public function __construct($type, $permissions)
    {
        $this->type = $type;
        $this->permissions = $permissions;
        if ($this->type == static::DEFAULT) {
            array_map(function ($v) {
                $v->setTarget();
            }, $this->permissions);
        }
    }
}

class PermissionDataObj
{
    private $entity;
    private $target;
    private $holder;
    private $mask;

    /**
     *
     * @param $target
     * @param $holder
     * @param $mask
     */
    public function __construct($target, $holder, $mask)
    {
        $this->entity = Entity::USERS;
        $this->target = $target;
        $this->holder = $holder;
        $this->mask = $mask;
    }

    public function setTarget($value = null)
    {
        $this->target = $value;
    }


}
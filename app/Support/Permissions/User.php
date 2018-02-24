<?php namespace App\Support\Permissions;

use App\Models\Entity;
use App\Models\GroupMember;
use App\Models\PermissionMask;
use App\Models\PermissionRecord;
use App\Models\PermissionStore;

class User extends Permission
{
    /**
     * @var int
     */
    private $noPermissionsBitmask = 0;
    /**
     * @var int
     */
    private $fullPermissionsBitmask;
    /**
     * @var array
     */
    private $userList;
    /**
     * @var PermissionStoreData
     */
    private $default;
    /**
     * @var PermissionStoreData
     */
    private $computed;

    public function __construct()
    {
        $this->fullPermissionsBitmask = array_sum(\App\Models\User::getConstants('PERMISSION'));
    }

    public function assignPermissions(){
        $this->userList = $this->arrangeUsersByUserID();
        $this->prepareAllForDb(
            $this->getUsersWithPermissions()
        );
        $this->populateDb();
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

    private function sqlGetUsersGroups()
    {
        return GroupMember::query()->select([
            'entity_types.entity_type_id',
            'users.user_id',
            'groups.group_mask'
        ])->group()->user()->entityType()->get();
    }

    /**
     * @param $usersWithPermissions
     * @return void
     */
    private function prepareAllForDb($usersWithPermissions)
    {
        $defaultPermissions = null;
        $permissionsToInsert = [];
        foreach ($this->userList as $user) {
            $usesDefaultPermissions = true;
            $tmp = [];
            foreach ($usersWithPermissions as $userWithPermission) {
                //A user with lower group status doesn't get to manipulate users above him.
                if ($user->group_mask < $this->userList[$userWithPermission->user_id]->group_mask) {
                    $tmp[] = new PermissionData(
                        $user->entity_type_id,
                        $this->userList[$userWithPermission->user_id]->entity_type_id,
                        $this->noPermissionsBitmask
                    );
                    $usesDefaultPermissions = false;
                    //A user can manipulate his own user entry.
                } elseif ($user->user_id == $userWithPermission->user_id) {
                    $tmp[] = new PermissionData(
                        $user->entity_type_id,
                        $this->userList[$userWithPermission->user_id]->entity_type_id,
                        $this->fullPermissionsBitmask
                    );
                    $usesDefaultPermissions = false;
                    //In all other cases, regular defined permissions apply.
                } else {
                    $tmp[] = new PermissionData(
                        $user->entity_type_id,
                        $this->userList[$userWithPermission->user_id]->entity_type_id,
                        $userWithPermission->permission_mask
                    );
                }
            }
            //If no special permissions have been processed, we stash away default permissions.
            if ($usesDefaultPermissions) {
                if (!is_null($defaultPermissions)) {
                    continue;
                }
                array_map(function ($v) {
                    $v->setTarget();
                }, $tmp);
                $defaultPermissions = new PermissionStoreData(
                    PermissionStoreData::DEFAULT, $tmp);
            } else {
                $permissionsToInsert[] = $tmp;
            }
        }
        $this->default = $defaultPermissions;
        $this->computed = new PermissionStoreData(PermissionStoreData::COMPUTED,
            array_flatten($permissionsToInsert, 1));
    }

    private function getUsersWithPermissions()
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

    private function sqlUsersWithPermissions()
    {
        return \App\Models\Permission::query()->select([
            'group_members.user_id as group_user_id',
            'users.user_id',
            'permission_mask',
            'entities.entity_id'
        ])->entityType()
            ->entityUser()
            ->leftGroupMember()
            ->leftUser()
            ->orderBy('group_user_id')
            ->orderBy('users.user_id')
            ->get();
    }

    private function populateDb()
    {
        \DB::beginTransaction();

        $default = $this->default->get();
            $defaultPermissionStoreId = (new PermissionStore)->insertGetId([]);
        foreach ($default as $permission) {
            (new PermissionMask())->insert([
                'permission_store_id' => $defaultPermissionStoreId,
                'permission_holder_id' => $permission->getHolder(),
                'permission_mask' => $permission->getMask()
            ]);
        }
        foreach ($this->userList as $user) {
            if ($this->computed->has($user->entity_type_id)) {
                $computed = $this->computed->get($user->entity_type_id);
                $permissionStoreId = (new PermissionStore)->insertGetId([]);
                foreach ($computed as $permission) {
                    (new PermissionMask())->insert([
                        'permission_store_id' => $permissionStoreId,
                        'permission_holder_id' => $permission->getHolder(),
                        'permission_mask' => $permission->getMask()
                    ]);
                }
                (new PermissionRecord)->insert([
                    'entity_id' => Entity::USERS,
                    'permission_target_id' => $user->entity_type_id,
                    'permission_store_id' => $permissionStoreId
                ]);
            } else {
                (new PermissionRecord)->insert([
                    'entity_id' => Entity::USERS,
                    'permission_target_id' => $user->entity_type_id,
                    'permission_store_id' => $defaultPermissionStoreId
                ]);
            }
        }

        \DB::commit();
        unset($this->userList);
    }
}
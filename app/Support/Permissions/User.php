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

    /**
     * Public facing method to populate DB permissions
     *
     * @throws \Exception
     */
    public function assignPermissions()
    {
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

    /**
     * SQL query that returns which users belong to which group. Allows us to determine which of the users' groups
     * is highest in the hierarchy. As of this writing, the hierarchy is determined using the group_mask column, where
     * higher status groups are given the lowest mask.
     *
     * @return mixed
     */
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

    /**
     * Arranging users in an array of type:
     *      array[user_id]=user permission
     *
     * @return array
     */
    private function getUsersWithPermissions()
    {
        $dbEntries = $this->sqlUsersWithPermissions();
        $usersWithPermissions = $tmp = [];
        foreach ($dbEntries as $dbEntry) {
            //Query does two left joins, so users without groups appear with a null group.
            //This "if" is for users without groups who have individual permissions assigned to them
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

    /**
     * Pulling users from the database who have permissions, either individually assigned or through groups.
     *
     * @return \Illuminate\Support\Collection
     */
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

    /**
     * After all the permission processing, this method inserts permissions into the database.
     *
     * @throws \Exception
     */
    private function populateDb()
    {
        \DB::beginTransaction();

        //There is only one default permission, which allows us not to have to create X times X permissions for everyone.
        $default = $this->default->get();
        $defaultPermissionStoreId = (new PermissionStore)->insertGetId([]);
        foreach ($default as $permission) {
            (new PermissionMask())->insert([
                'permission_store_id' => $defaultPermissionStoreId,
                'permission_holder_id' => $permission->getHolder(),
                'permission_mask' => $permission->getMask(),
                'permission_is_default' => true
            ]);
        }
        //For users with permissions that needed some processing, we write them user by user.
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
                //If there are no computed permissions for the user, we assign the default one.
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
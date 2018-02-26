<?php namespace App\Support\Permissions;

use App\Models\GroupMember;

abstract class Permission
{
    /**
     * @var int
     */
    protected $fullPermissionsBitmask;
    /**
     * @var array
     */
    protected $allUsersInfo;
    /**
     * @var PermissionStoreData
     */
    protected $default;
    /**
     * @var PermissionStoreData
     */
    protected $computed;
    /**
     * @var int
     */
    protected $entityId;

    protected function __construct($modelClass, $entityId)
    {
        $this->fullPermissionsBitmask = array_sum(forward_static_call([$modelClass, 'getConstants'], 'PERMISSION'));
        $this->entityId = $entityId;
    }

    /**
     * Arranging users in an array of type:
     *      array[user_id]=user permission
     *
     * Important note: entries are ordered so that users with individually assigned permissions appear first,
     * and users with group related permissions appear next.
     * That way, we make sure that user permissions take priority over group permissions.
     *
     * @return array
     */
    protected function getUsersWithPermissions()
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
    protected function sqlUsersWithPermissions()
    {
        return \App\Models\Permission::query()->select([
            'group_members.user_id as group_user_id',
            'users.user_id',
            'permission_mask',
            'permissions.entity_id',
            'users.username'
        ])->entity($this->entityId)
            ->entityType()
            ->leftGroupMember()
            ->leftUser()
            ->orderBy('group_user_id')
            ->orderBy('users.user_id')
            ->get();
    }

    /**
     * SQL query that returns which users belong to which group. Allows us to determine which of the users' groups
     * is highest in the hierarchy. The hierarchy is determined using the group_mask column, where
     * higher status groups are given the lowest mask.
     *
     * @param array $userIdList
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function sqlGetUsersGroups($userIdList = null)
    {
        $builder = GroupMember::query()->select([
            'entity_types.entity_type_id',
            'users.user_id',
            'groups.group_mask',
            'users.username'])->group()->user()->entityType();
        if (!is_null($userIdList)) {
            $builder->whereIn('users.user_id', $userIdList);
        }
        return $builder->get();
    }

}
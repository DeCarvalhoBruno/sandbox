<?php namespace App\Support\Permissions;

use App\Contracts\Enumerable;
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
    /**
     * @var array
     */
    private static $permissionBoundEntities = [
        User::class,
        Group::class,
    ];

    protected function __construct($modelClass, $entityId)
    {
        try {
            $rc = new \ReflectionClass($modelClass);
        } catch (\ReflectionException $e) {
            throw new \UnexpectedValueException(sprintf('Class %s does not exist.', $modelClass));
        }
        if (!$rc->implementsInterface(Enumerable::class)) {
            throw new \UnexpectedValueException(sprintf('Class %s must implement Enumerable, method getConstants has to be called.',
                $modelClass));
        }

        $this->fullPermissionsBitmask = array_sum(forward_static_call([$modelClass, 'getConstants'], 'PERMISSION'));
        $this->entityId = $entityId;
    }

    public static function assignToAll()
    {
        foreach (self::$permissionBoundEntities as $permissionObject) {
            (new $permissionObject)->assignPermissions();
        }
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
        $usersWithPermissions = $alreadyHasIndividualPermissions = $maxGroupPermission = [];
        foreach ($dbEntries as $dbEntry) {
            //Query does two left joins, so users without groups appear with a null group.
            //This "if" is for users who have individual permissions assigned to them.
            //Individual permissions bypass group permissions.
            if (!is_null($dbEntry->group_user_id) && !isset($alreadyHasIndividualPermissions[$dbEntry->group_user_id])) {
                $dbEntry->user_id = $dbEntry->group_user_id;
                //If a user is a member of multiple groups with different permission masks
                //we choose the highest of permissions




                //@TODO: test an addition of permissions when the user has permissions from multiple groups







                if (!isset($maxGroupPermission[$dbEntry->group_user_id])) {
                    $maxGroupPermission[$dbEntry->user_id] = $dbEntry->permission_mask;
                    $usersWithPermissions[$dbEntry->group_user_id] = (object)$dbEntry->toArray();
                } elseif ($dbEntry->permission_mask > $maxGroupPermission[$dbEntry->group_user_id]) {
                    $maxGroupPermission[$dbEntry->group_user_id] = $dbEntry->permission_mask;
                    $usersWithPermissions[$dbEntry->group_user_id] = (object)$dbEntry->toArray();
                }
            } elseif (!is_null($dbEntry->user_id)) {
                $alreadyHasIndividualPermissions[$dbEntry->user_id] = (object)$dbEntry->toArray();
                $usersWithPermissions[$dbEntry->user_id] = (object)$dbEntry->toArray();
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
            'permissions.entity_id'
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
            'groups.group_mask'
        ])->group()->user()->entityType();
        if (!is_null($userIdList)) {
            $builder->whereIn('users.user_id', $userIdList);
        }
        return $builder->get();
    }

}
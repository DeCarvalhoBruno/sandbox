<?php namespace Naraki\Permission\Providers;

use App\Contracts\RawQueries;
use App\Models\Entity;
use App\Models\EntityType;
use App\Support\Providers\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Naraki\Permission\Contracts\HasPermissions;
use Naraki\Permission\Contracts\Permission as PermissionInterface;
use Naraki\Permission\Models\Permission as PermissionModel;

/**
 * @method \Naraki\Permission\Models\Permission createModel(array $attributes = [])
 * @method \Naraki\Permission\Models\Permission getOne($id, $columns = ['*'])
 */
class Permission extends Model implements PermissionInterface
{
    protected $model = PermissionModel::class;

    /**
     * @param array $permissionData
     * @param int $entityTypeId
     * @param int $entityId
     */
    public function updateIndividual($permissionData, $entityTypeId, $entityId)
    {
        if (!is_int($entityTypeId)) {
            return;
        }
        $query = $this
            ->buildWithScopes(
                ['permission_id', 'permission_mask', 'entities.entity_id'],
                ['entityAll' => $entityTypeId, 'entityType']
            );
        $result = $this->createModel()->applyEntityScope($query, $entityId)->get()->toArray();

        $currentPermissions = [];
        foreach ($result as $v) {
            $currentPermissions[$v['entity_id']] = (object)$v;
        }

        foreach ($permissionData as $entityId => $newPermissionMask) {
            if (isset($currentPermissions[$entityId])) {
                PermissionModel::query()
                    ->where('permission_id', $currentPermissions[$entityId]->permission_id)
                    ->update(['permission_mask' => $newPermissionMask]);
            } else {
                $this->createModel([
                    'entity_type_id' => $entityTypeId,
                    'entity_id' => $entityId,
                    'permission_mask' => $newPermissionMask
                ])->save();
            }
        }
    }

    /**
     * Gets all root permissions on groups which we see as "max" permissions
     * as well as the current group permissions.
     *
     * @param int $entityTypeId
     * @return array
     */
    public function getRootAndGroupPermissions($entityTypeId = null)
    {
        $entities = [EntityType::ROOT_GROUP_ENTITY_TYPE_ID];
        if (!is_null($entityTypeId)) {
            array_push($entities, $entityTypeId);
        }
        $results = $this
            ->select(['permission_id', 'permission_mask', 'entities.entity_id', 'entity_type_id'])
            ->entityAll($entities)->get();
        $permission = [];
        foreach ($results as $result) {
            $type = ($result->entity_type_id == EntityType::ROOT_GROUP_ENTITY_TYPE_ID) ? 'default' : 'computed';
            $permission[$type][Entity::getConstantName($result->entity_id)] =
                Entity::createModel($result->entity_id, [], HasPermissions::class)
                    ->getReadablePermissions($result->permission_mask, true);
        }
        //We're supposed to get an array with computed and default permissions but some users
        // don't have permissions on anything.
        if (!isset($permission['computed'])) {
            $permission['computed'] = [];
        }
        return $permission;
    }

    /**
     * @param int $entityTypeID
     * @return mixed
     */
    public function getAllUserPermissions(int $entityTypeID)
    {
        return (app(RawQueries::class))->getAllUserPermissions($entityTypeID);
    }

    /**
     * @param int $entityTypeID
     * @return \stdClass
     */
    public function cacheUserPermissions(int $entityTypeID): ?\stdClass
    {
        $cacheKey = 'permissions.' . $entityTypeID;
        $permissionsObj = Cache::get($cacheKey);
        if (is_null($permissionsObj)) {
            $permissionsObj = $this->getAllUserPermissions($entityTypeID);
            $permissions = (object)$permissionsObj->computed;
            if (!isset($permissions->system) && !isset($permissions->system['Login'])) {
                return null;
            }
            Cache::put($cacheKey, serialize($permissions), 60 * 30);
        } else {
            $permissions = unserialize($permissionsObj);
        }
        return $permissions;
    }

}
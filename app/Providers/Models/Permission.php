<?php namespace App\Providers\Models;

use App\Contracts\HasPermissions;
use App\Contracts\Models\Permission as PermissionInterface;
use App\Events\UpdatedUser;
use App\Models\Entity;
use App\Models\EntityType;
use App\Models\Permission as PermissionModel;

/**
 * @method \App\Models\Permission createModel(array $attributes = [])
 * @method \App\Models\Permission getOne($id, $columns = ['*'])
 */
class Permission extends Model implements PermissionInterface
{
    protected $model = PermissionModel::class;

    public function updateIndividual($permissionData, $entityTypeId)
    {
        if (!is_int($entityTypeId)) {
            return;
        }
        $result = $this->createModel()->newQuery()
            ->select(['permission_id', 'permission_mask', 'entities.entity_id'])
            ->entityAll($entityTypeId)->entityType()->user()->get()->toArray();
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
        event(new UpdatedUser);
    }

    public function getRootAndGroupPermissions($entityTypeId)
    {
        $results= $this->createModel()->newQuery()->newQuery()
            ->select(['permission_id', 'permission_mask', 'entities.entity_id','entity_type_id'])
            ->entityAll([$entityTypeId,EntityType::ROOT_GROUP_ENTITY_TYPE_ID])->get();
        $permission = [];
        foreach ($results as $result) {
            $type=($result->entity_type_id==EntityType::ROOT_GROUP_ENTITY_TYPE_ID)?'default':'computed';
            $permission[$type][trans_choice(sprintf('ajax.db.%s',
                Entity::getModelPresentableName($result->entity_id)),2)] =
                Entity::createModel($result->entity_id,[],HasPermissions::class)
                    ->getReadablePermissions($result->permission_mask, true);
        }
        //We're supposed to get an array with computed and default permissions but some users
        // don't have permissions on anything.
        if (!isset($permission['computed'])) {
            $permission['computed'] = [];
        }
        return $permission;
    }

}
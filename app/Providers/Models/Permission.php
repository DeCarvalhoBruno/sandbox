<?php namespace App\Providers\Models;

use App\Contracts\Models\Permission as PermissionInterface;
use App\Events\UpdatedUser;
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

}
<?php namespace App\Providers\Models;

use App\Contracts\Models\Permission as PermissionInterface;
/**
 * @method \App\Models\Permission createModel(array $attributes = [])
 * @method \App\Models\Permission getOne($id, $columns = ['*'])
 */
class Permission extends Model implements PermissionInterface
{
    protected $model = \App\Models\Permission::class;

    public function updateIndividual($permissionData,$entityTypeId)
    {
        $result = $this->createModel()->newQuery()->select(['permission_mask','entities.entity_id'])->entityAll($entityTypeId)->entityType()->user()->get()->toArray();
        $currentPermissions=[];
        foreach($result as $v){
            $currentPermissions[$v['entity_id']]=$v['permission_mask'];
        }
        return [$currentPermissions,$permissionData];

    }

}
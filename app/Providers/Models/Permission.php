<?php namespace App\Providers\Models;

use App\Contracts\Models\Permission as PermissionInterface;
/**
 * @method \App\Models\Permission createModel(array $attributes = [])
 * @method \App\Models\Permission getOne($id, $columns = ['*'])
 */
class Permission extends Model implements PermissionInterface
{
    protected $model = \App\Models\Permission::class;

}
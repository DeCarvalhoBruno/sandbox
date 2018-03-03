<?php namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PermissionMask extends Model
{
    public $timestamps = false;

    protected $fillable = ['permission_store_id', 'permission_holder_id', 'permission_mask', 'permission_is_default'];

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param $userEntityId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopePermissionStore(Builder $builder, $userEntityId)
    {
        return $builder->join('permission_stores', function ($q) use ($userEntityId) {
            $q->on('permission_masks.permission_store_id', '=', 'permission_stores.permission_store_id')
                ->where('permission_masks.permission_holder_id', '=', $userEntityId)
                ->where('permission_is_default', '=', true);
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param $entityId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopePermissionRecord(Builder $builder, $entityId)
    {
        return $builder->join('permission_records', function ($q) use ($entityId) {
            $q->on('permission_stores.permission_store_id', '=', 'permission_records.permission_store_id')
                ->where('permission_records.entity_id', '=', $entityId);
        });
    }

    public static function getDefaultPermission($entityTypeId, $entityId)
    {
        return static::query()->select('permission_mask')
            ->permissionStore($entityTypeId)
            ->permissionRecord($entityId)
            ->groupBy('permission_masks.permission_store_id')
            ->groupBy('permission_masks.permission_mask')
            ->pluck('permission_mask')
            ->pop();
    }
}

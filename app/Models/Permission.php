<?php namespace App\Models;

use App\Traits\Models\DoesSqlStuff;
use App\Traits\Models\HasAnEntity;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use DoesSqlStuff;

    public $primaryKey = 'permission_id';
    public $timestamps = false;
    protected $fillable = ['entity_type_id', 'entity_id', 'permission_mask'];

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntityType($builder)
    {
        return $this->joinReverse($builder, EntityType::class);
    }

    /**
     * Getting permissions pertaining to a particular entity.
     *
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $entityId
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntity($builder, $entityId)
    {
        return $builder->join('entities', function ($q) use ($entityId) {
            $q->on('permissions.entity_id', '=', 'entities.entity_id')
                ->where('entities.entity_id', '=', $entityId);
        });
    }

    /**
     * Getting permissions for all entities
     *
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int|array $entityTypeIds
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeEntityAll($builder, $entityTypeIds)
    {
        return $builder->join('entities', function ($q) use ($entityTypeIds) {
            $q->on('permissions.entity_id', '=', 'entities.entity_id');
            if (is_array($entityTypeIds)) {
                $q->whereIn('permissions.entity_type_id', $entityTypeIds);
            } else {
                $q->where('permissions.entity_type_id', '=', $entityTypeIds);
            }
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLeftGroupMember($builder)
    {
        return $builder->leftJoin('group_members', function ($q) {
            $q->on('group_members.group_id', '=', 'entity_types.entity_type_target_id')
                ->where('entity_types.entity_id', '=', Entity::GROUPS);
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeGroup($builder)
    {
        return $builder->join('groups', function ($q) {
            $q->on('groups.group_id', '=', 'entity_types.entity_type_target_id')
                ->where('entity_types.entity_id', '=', Entity::GROUPS);
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeLeftUser($builder)
    {
        return $builder->leftJoin('users', function ($q) {
            $q->on('users.user_id', '=', 'entity_types.entity_type_target_id')
                ->where('entity_types.entity_id', '=', Entity::USERS);
        });
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeUser($builder)
    {
        return $builder->join('users', function ($q) {
            $q->on('users.user_id', '=', 'entity_types.entity_type_target_id')
                ->where('entity_types.entity_id', '=', Entity::USERS);
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $entityId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyEntityScope($builder, $entityId)
    {
        $method = ucfirst(str_singular(Entity::getConstantName($entityId)));
        return call_user_func([$builder, $method]);
    }

}

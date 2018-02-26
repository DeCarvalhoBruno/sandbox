<?php namespace App\Models;

use App\Traits\Models\DoesSqlStuff;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use DoesSqlStuff;

    public $primaryKey = 'permission_id';
    public $timestamps = false;
    protected $fillable = ['entity_type_id', 'entity_id'];

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEntityType($builder)
    {
        return $this->joinWithJoinedPK($builder, EntityType::class);
    }

    /**
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
    public function scopeLeftUser($builder)
    {
        return $builder->leftJoin('users', function ($q) {
            $q->on('users.user_id', '=', 'entity_types.entity_type_target_id')
                ->where('entity_types.entity_id', '=', Entity::USERS);
        });
    }

}

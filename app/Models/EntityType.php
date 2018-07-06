<?php namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    public $timestamps = false;
    public static $entityColumn = 'entity_types.entity_type_target_id';
    public static $simpleEntityColumn = 'entity_type_target_id';
    protected $primaryKey = 'entity_type_id';
    protected $fillable = ['entity_id', 'entity_type_target_id', 'entity_type_id'];
    const ROOT_ENTITY_TYPE_ID = 2;
    const ROOT_GROUP_ENTITY_TYPE_ID = 3;

    /**
     * Gets the entity type ID with an entity ID
     *
     * Example: getEntityTypeID(15,5)
     * returns user entity type id for user ID 5 (15 is the user entity ID)
     * ID 5 could belong to a group, forum post, etc., so we specify the entityId
     * so the query can start from the user table
     *
     * @see \App\Models\Entity
     * @param $entityID
     * @param int|array|\stdClass $filter
     *
     * @return array|int
     */
    public static function getEntityTypeID($entityID, $filter)
    {
        $baseBuilder = static::getEntityInfo($entityID, $filter);
        if (is_array($filter)) {
            return $baseBuilder->pluck('entity_type_id')->all();
        }
        return $baseBuilder->value('entity_type_id');
    }

    /**
     * Gets the entity type ID with an entity ID
     *
     * Example: getEntityTypeID(15,5)
     * returns all the user entity type info for user ID 5 (15 is the user entity ID)
     *
     * @see \App\Models\Entity
     *
     * @param int $entityID
     * @param int|array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getEntityInfo($entityID, $filter)
    {
        $baseBuilder = static::query();
        $builderWithEntity = call_user_func([$baseBuilder, 'entityType'], $entityID)
            ->where('entity_id', $entityID);

        if (is_int($filter)) {
            $builderWithEntity->where('entity_type_target_id', $filter);
        } elseif (is_array($filter)) {
            $builderWithEntity->whereIn('entity_type_target_id', $filter);
        } else {
            $builderWithEntity->where(static::getEntityNameColumn($entityID), '=', $filter);
        }

        return $builderWithEntity;
    }

    /**
     * @param $entityID
     * @param $entityTypeID
     * @return mixed
     */
    public static function buildQueryFromEntity($entityID, $entityTypeID)
    {
        $class = Entity::getModelClassNamespace($entityID);
        $baseBuilder = new $class();

        $builderWithEntity = $baseBuilder->newQuery()
            ->join(
                'entity_types',
                $baseBuilder->getQualifiedKeyName(), '=',
                'entity_types.entity_type_target_id'
            )->where('entity_types.entity_id', $entityID);
        if (is_array($entityTypeID)) {
            $builderWithEntity->whereIn('entity_type_id', $entityTypeID);
        } else {
            $builderWithEntity->where('entity_type_id', $entityTypeID);
        }

        return $builderWithEntity;
    }

    /**
     * @param $entityID
     * @param $targetID
     * @return mixed
     */
    public static function buildQueryFromTarget($entityID, $targetID)
    {
        $class = Entity::getModelClassNamespace($entityID);
        $baseBuilder = new $class();

        $builderWithEntity = $baseBuilder->newQuery()
            ->join(
                'entity_types',
                $baseBuilder->getQualifiedKeyName(), '=',
                'entity_types.entity_type_target_id'
            )->where('entity_types.entity_id', $entityID);
        if (is_array($targetID)) {
            $builderWithEntity->whereIn('entity_type_target_id', $targetID);
        } else {
            $builderWithEntity->where('entity_type_target_id', $targetID);
        }

        return $builderWithEntity;
    }

    /**
     * Returns the name of the column the entity uses to store names,
     * the most common being "name", but it can be something else like "title"
     *
     * @param $entityID
     *
     * @return string
     */
    public static function getEntityNameColumn($entityID)
    {
        $targetType = Entity::getModelClassNamespace($entityID);

        return $targetType::$nameColumn;
    }

    /**
     * @param int $entityID
     * @param int $targetID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getByTargetID($entityID, $targetID)
    {
        return static::query()
            ->where('entity_type_target_id', $targetID)
            ->where('entity_id', $entityID);
    }

    /**
     * Makes the join on this table for any entity
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $entityID
     *
     * @return mixed
     */
    public function scopeEntityType(Builder $query, $entityID)
    {
        $class = Entity::getModelClassNamespace($entityID);
        $primaryKey = (new $class)->getQualifiedKeyName();

        return $query->join(
            Entity::getConstantName($entityID),
            $primaryKey, '=', 'entity_types.entity_type_target_id'
        );
    }

    /**
     * @link https://laravel.com/docs/5.6/eloquent#query-scopes
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int $entityId
     * @param array $userIdList
     * @return \Illuminate\Database\Eloquent\Builder $builder
     */
    public function scopeHighestGroup(Builder $builder, $entityId, $userIdList)
    {
        return $builder->joinSub(User::queryHighestRankedGroup($userIdList),
            'users_highest_group',
            'entity_types.entity_type_target_id',
            '=',
            'users_highest_group.user_id')
            ->where('entity_types.entity_id', '=', $entityId);
    }
}

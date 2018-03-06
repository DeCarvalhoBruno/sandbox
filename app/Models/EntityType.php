<?php namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    public $timestamps = false;
    public static $entityColumn = 'entity_types.entity_type_target_id';
    public static $simpleEntityColumn = 'entity_type_target_id';
    protected $primaryKey = 'entity_type_id';
    protected $fillable = ['entity_id', 'entity_type_target_id','entity_type_id'];
    const ROOT_ENTITY_TYPE_ID=2;


    /**
     * Gets the entity type ID with an entity ID
     *
     * Example: getEntityTypeID(15,5)
     * returns user entity type id for user ID 5 (15 is the user entity ID)
     *
     * @param $entityID
     * @param $targetID
     *
     * @return array|int
     */
    public static function getEntityTypeID($entityID, $targetID)
    {
        $baseBuilder = static::query();
        $builderWithEntity = call_user_func([$baseBuilder, 'entityType'], $entityID)
            ->where('entity_id', $entityID);
        if (is_array($targetID)) {
            return $builderWithEntity->whereIn('entity_type_target_id',
                $targetID)->pluck('entity_type_id')->all();
        } else {
            return $builderWithEntity->where('entity_type_target_id', $targetID)->value('entity_type_id');
        }
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
     * @param int $targetID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getEntityInfo($entityID, $targetID)
    {
        $baseBuilder = static::query();
        $builderWithEntity = call_user_func([$baseBuilder, 'entityType'], $entityID)
            ->where('entity_id', $entityID);
        if (is_array($targetID)) {
            $builderWithEntity->whereIn('entity_type_target_id', $targetID);
        } else {
            $builderWithEntity->where('entity_type_target_id', $targetID);
        }

        return $builderWithEntity;
    }

    public static function buildQueryFromEntity($entityID, $entityTypeID)
    {
        $class = Entity::getModelClassName($entityID);
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

    public static function buildQueryFromTarget($entityID, $targetID)
    {
        $class = Entity::getModelClassName($entityID);
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
        $targetType = Entity::getModelClassName($entityID);

        return call_user_func([$targetType, 'getNameColumn']);
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
        $table = Entity::getConstantName($entityID);
        $class = Entity::getModelClassName($entityID);
        $primaryKey = (new $class)->getKeyName();

        return $query->join(
            Entity::getConstantName($entityID),
            sprintf('%s.%s', $table, $primaryKey), '=', 'entity_types.entity_type_target_id'
        );
    }
}

<?php namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SystemEntityType extends Model
{
    public $timestamps = false;
    public static $entityColumn = 'system_entity_types.system_entity_type_target_id';
    public static $simpleEntityColumn = 'system_entity_type_target_id';
    protected $primaryKey = 'system_entity_type_id';
    protected $fillable = ['system_entity_id', 'system_entity_type_target_id'];


    /**
     * Gets the entity type ID with an entity ID
     *
     * Example: getEntityTypeID(15,5)
     * returns user entity type id for user ID 5 (15 is the user entity ID)
     *
     * @param $systemEntityID
     * @param $targetID
     *
     * @return array|int
     */
    public static function getEntityTypeID($systemEntityID, $targetID)
    {
        $baseBuilder = static::query();
        $builderWithEntity = call_user_func([$baseBuilder, 'systemEntityType'], $systemEntityID)
            ->where('system_entity_id', $systemEntityID);
        if (is_array($targetID)) {
            return $builderWithEntity->whereIn('system_entity_type_target_id',
                $targetID)->pluck('system_entity_type_id')->all();
        } else {
            return $builderWithEntity->where('system_entity_type_target_id', $targetID)->value('system_entity_type_id');
        }
    }

    /**
     * Gets the entity type ID with an entity ID
     *
     * Example: getEntityTypeID(15,5)
     * returns all the user entity type info for user ID 5 (15 is the user entity ID)
     *
     * @see \App\Models\SystemEntity
     *
     * @param int $systemEntityID
     * @param int $targetID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getEntityInfo($systemEntityID, $targetID)
    {
        $baseBuilder = static::query();
        $builderWithEntity = call_user_func([$baseBuilder, 'systemEntityType'], $systemEntityID)
            ->where('system_entity_id', $systemEntityID);
        if (is_array($targetID)) {
            $builderWithEntity->whereIn('system_entity_type_target_id', $targetID);
        } else {
            $builderWithEntity->where('system_entity_type_target_id', $targetID);
        }

        return $builderWithEntity;
    }

    public static function buildQueryFromEntity($systemEntityID, $systemEntityTypeID)
    {
        $class = SystemEntity::getModelClassName($systemEntityID);
        $baseBuilder = new $class();

        $builderWithEntity = $baseBuilder->newQuery()
            ->join(
                'system_entity_types',
                $baseBuilder->getQualifiedKeyName(), '=',
                'system_entity_types.system_entity_type_target_id'
            )->where('system_entity_types.system_entity_id', $systemEntityID);
        if (is_array($systemEntityTypeID)) {
            $builderWithEntity->whereIn('system_entity_type_id', $systemEntityTypeID);
        } else {
            $builderWithEntity->where('system_entity_type_id', $systemEntityTypeID);
        }

        return $builderWithEntity;
    }

    public static function buildQueryFromTarget($systemEntityID, $targetID)
    {
        $class = SystemEntity::getModelClassName($systemEntityID);
        $baseBuilder = new $class();

        $builderWithEntity = $baseBuilder->newQuery()
            ->join(
                'system_entity_types',
                $baseBuilder->getQualifiedKeyName(), '=',
                'system_entity_types.system_entity_type_target_id'
            )->where('system_entity_types.system_entity_id', $systemEntityID);
        if (is_array($targetID)) {
            $builderWithEntity->whereIn('system_entity_type_target_id', $targetID);
        } else {
            $builderWithEntity->where('system_entity_type_target_id', $targetID);
        }

        return $builderWithEntity;
    }

    /**
     * Returns the name of the column the entity uses to store names,
     * the most common being "name", but it can be something else like "title"
     *
     * @param $systemEntityID
     *
     * @return string
     */
    public static function getEntityNameColumn($systemEntityID)
    {
        $targetType = SystemEntity::getModelClassName($systemEntityID);

        return call_user_func([$targetType, 'getNameColumn']);
    }

    /**
     * @param int $systemEntityID
     * @param int $targetID
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getByTargetID($systemEntityID, $targetID)
    {
        return static::query()
            ->where('system_entity_type_target_id', $targetID)
            ->where('system_entity_id', $systemEntityID);
    }

    /**
     * Makes the join on this table for any entity
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $systemEntityID
     *
     * @return mixed
     */
    public function scopeSystemEntityType(Builder $query, $systemEntityID)
    {
        $table = SystemEntity::getConstantName($systemEntityID);
        $class = SystemEntity::getModelClassName($systemEntityID);
        $primaryKey = (new $class)->getKeyName();

        return $query->join(
            SystemEntity::getConstantName($systemEntityID),
            sprintf('%s.%s', $table, $primaryKey), '=', 'system_entity_types.system_entity_type_target_id'
        );
    }
}

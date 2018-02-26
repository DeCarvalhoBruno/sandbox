<?php namespace App\Models;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use Enumerable;

    const GROUPS = 0x44c;               //1100
    const GROUP_MEMBERS = 0x44d;        //1101
    const SYSTEM = 0xaf0;               //2800
    const USERS = 0xc1c;                //3100

    public $timestamps = false;
    protected $table = 'entities';
    protected $primaryKey = 'entity_id';
    protected $fillable = ['entity_id', 'entity_name'];
    public static $classMap = [
    ];

    public static function createModel($entityID, array $attributes = [])
    {
        $class = static::getModelClassName($entityID);
        if (class_exists($class)) {
            return new $class($attributes);
        }

        return null;
    }

    public static function getModelClass($entityID)
    {
        $entities = array_flip(static::getConstants());
        if (isset($entities[$entityID])) {
            return ucfirst(camel_case(str_singular(strtolower($entities[$entityID]))));
        }

        return null;
    }

    public static function getModelPrimaryKey($entityID, $getQualifiedName = false)
    {
        $class = self::getModelClassName($entityID);
        $instance = new $class();
        if ($getQualifiedName === false) {
            return $instance->getKeyName();
        }

        return $instance->getQualifiedKeyName();
    }

    public static function getModelClassName($entityID)
    {
        $className = static::getModelClass($entityID);
        $classPath = sprintf('\App\Models\%s',
            isset(static::$classMap[$className]) ?
                static::$classMap[$className] :
                $className);
        if (class_exists($classPath)) {
            return $classPath;
        }
        throw new \UnexpectedValueException(sprintf('Class %s does not exist. (%s)', $className, $entityID));
    }

    public static function getModelSimpleName($entityID)
    {
        $entities = array_flip(static::getConstants());
        if (isset($entities[$entityID])) {
            return str_singular(strtolower($entities[$entityID]));
        }

        return null;
    }


}

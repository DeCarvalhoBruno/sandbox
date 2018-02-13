<?php namespace App\Models;

use App\Traits\EnumerableTrait;
use Illuminate\Database\Eloquent\Model;

class SystemEntity extends Model
{
    use EnumerableTrait;

    const SYSTEM = 0x0001;
    const PEOPLE = 0x9001;
    const USERS = 0xc350; //50000

    public $timestamps = false;
    protected $table = 'system_entities';
    protected $primaryKey = 'system_entity_id';
    protected $fillable = ['system_entity_id', 'system_entity_name'];
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

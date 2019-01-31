<?php namespace App\Models;

use App\Traits\Enumerable;
use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    use Enumerable;

    const BLOG_POSTS = 0x12c;           //300
    const GROUPS = 0x44c;               //1100
    const GROUP_MEMBERS = 0x44d;        //1101
    const SYSTEM = 0xaf0;               //2800
    const USERS = 0xc1c;                //3100

    public $timestamps = false;
    protected $table = 'entities';
    protected $primaryKey = 'entity_id';
    protected $fillable = ['entity_id', 'entity_name'];
    /**
     * @var array Used in case a specific model isn't in \App\Models
     */
    public static $classMap = [
        'BlogPost' => 'Blog\BlogPost'
    ];

    /**
     * Creates an instance of the model using its entity_id
     *
     * @param int $entityID
     * @param array $attributes
     * @param string $testContract
     * @return \Illuminate\Database\Eloquent\Model|\App\Contracts\HasPermissions
     */
    public static function createModel($entityID, array $attributes = [], $testContract = null)
    {
        $class = static::getModelClassNamespace($entityID);
        if (class_exists($class)) {
            $o = new $class($attributes);
            if (!is_null($testContract) && !($o instanceof $testContract)) {
                throw new \UnexpectedValueException(
                    sprintf('Model %s is supposed to be an instance of %s' . !($class instanceof $testContract),
                        $class,
                        $testContract)
                );
            }
            return $o;
        }
        return null;
    }

    /**
     * Returns the model's full namespace using its entity_id
     * Useful for instantiating a model.
     *
     * @param int $entityID
     * @param bool $prefixWithBackslash
     * @return string
     */
    public static function getModelClassNamespace($entityID, $prefixWithBackslash = true)
    {
        try {
            $className = static::getModelClass($entityID);
            $classNamespace = sprintf('\App\Models\%s',
                isset(static::$classMap[$className]) ?
                    static::$classMap[$className] :
                    $className);
            if (class_exists($classNamespace)) {
                return ($prefixWithBackslash) ? $classNamespace : substr($classNamespace, 1);
            }
            throw new \UnexpectedValueException(sprintf('Class %s does not exist. (%s)', $className, $entityID));
        } catch (\ReflectionException $re) {
            throw new \UnexpectedValueException('Reflection failed .' . $re->getMessage());
        }
    }

    /**
     * Returns the model's class name using its entity_id
     *
     * @param int $entityID
     * @return null|string
     */
    public static function getModelClass($entityID)
    {
        $entities = array_flip(static::getConstants());
        if (isset($entities[$entityID])) {
            return ucfirst(camel_case(str_singular(strtolower($entities[$entityID]))));
        }
        return null;
    }

    /**
     * Get the model's primary key using its entity_id
     *
     * @param int $entityID
     * @param bool $getQualifiedName Should the table name be included in the result
     * @return mixed
     */
    public static function getModelPrimaryKey($entityID, $getQualifiedName = false)
    {
        $class = self::getModelClassNamespace($entityID);
        $instance = new $class();
        if ($getQualifiedName === false) {
            return $instance->getKeyName();
        }

        return $instance->getQualifiedKeyName();
    }

    /**
     * Get the model's presentable name, i.e 'Users', 'Groups', using its entity_id
     * @param $entityID
     * @return null|string
     */
    public static function getModelPresentableName($entityID)
    {
        $entities = array_flip(static::getConstants());
        if (isset($entities[$entityID])) {
            return str_singular(strtolower($entities[$entityID]));
        }
        return null;
    }


}

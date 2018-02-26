<?php namespace App\Traits;

trait Enumerable
{
    private static $constCacheArray = null;

    /**
     * @param string $prefix
     * @return mixed
     * @throws \ReflectionException
     */
    public static function getConstants($prefix = null)
    {
        if (static::$constCacheArray == null) {
            static::$constCacheArray = [];
        }
        if (!is_null($prefix)) {
            $fn = function ($val, $key) use ($prefix) {
                return is_int($val) && strpos($key, $prefix) !== false;
            };
        } else {
            $fn = function ($val) use ($prefix) {
                return is_int($val);
            };
        }

        $calledClass = get_called_class();
        if (!array_key_exists($calledClass, static::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);

            static::$constCacheArray[$calledClass] = array_filter(
                $reflect->getConstants(), $fn, ARRAY_FILTER_USE_BOTH
            );
        }

        return static::$constCacheArray[$calledClass];
    }

    /**
     * @param string $name
     * @param bool $strict
     *
     * @return bool
     * @throws \ReflectionException
     */
    public static function isValidName($name, $strict = false)
    {
        $constants = static::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));

        return in_array(strtolower($name), $keys);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     * @throws \ReflectionException
     */
    public static function isValidValue($value)
    {
        $values = array_values(static::getConstants());

        return in_array($value, $values, $strict = true);
    }

    /**
     * @param mixed $name
     *
     * @return null
     * @throws \ReflectionException
     */
    public static function getConstantNameByID($name)
    {
        $constants = array_flip(static::getConstants());
        if (isset($constants[$name])) {
            return $constants[$name];
        }
        return null;
    }

    /**
     * @param int $id
     *
     * @return boolean
     * @throws \ReflectionException
     */
    public static function getConstantByID($id)
    {
        $constants = array_flip(static::getConstants());
        if (isset($constants[$id])) {
            return $constants[$id];
        }
        return null;
    }

    /**
     * @param mixed $id
     *
     * @return string
     * @throws \Exception
     */
    public static function getConstantName($id)
    {
        $name = static::getConstantNameByID($id);
        if (!is_null($name)) {
            return strtolower($name);
        }
        throw new \Exception(sprintf('Constant %s does not exist.', $id));
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getConstant($name)
    {
        $c = new \ReflectionClass(static::class);
        $id = $c->getConstant($name);
        if (!is_null($id)) {
            return $id;
        }
        throw new \Exception(sprintf('Constant %s does not exist.', $name));
    }

}




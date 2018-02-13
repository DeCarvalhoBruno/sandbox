<?php namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @method static void bindJsVariablesToView
 * @method static void put($key, $value)
 * @method static void putArray(Array $array)
 *
 * @see \App\Support\JavaScript\Transformers\Transformer
 */
class JavaScript extends Facade
{
    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'JavaScript';
    }
}
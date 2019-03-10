<?php

namespace App\Support\Database\ElasticSearch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Support\Database\ElasticSearch\DSL\SearchBuilder search()
 * @method static \App\Support\Database\ElasticSearch\DSL\SuggestionBuilder suggest()
 */
class ElasticSearch extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return \App\Support\Database\ElasticSearch\Manager
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['elastic'];
    }
}

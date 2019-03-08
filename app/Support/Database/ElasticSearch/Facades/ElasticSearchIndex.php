<?php namespace App\Support\Database\ElasticSearch\Facades;

use Illuminate\Support\Facades\Facade;


class ElasticSearchIndex extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return \App\Support\Database\ElasticSearch\Manager
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['elastic']->connection()->getIndexBuilder();
    }
}

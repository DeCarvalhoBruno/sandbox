<?php namespace App\Support\Database\ElasticSearch;

use App\Support\Database\ElasticSearch\Facades\ElasticSearchIndex;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use App\Support\Database\ElasticSearch\Manager as ElasticSearchManager;

class ElasticServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('elastic', function (Application $app) {
            return new ElasticSearchManager($app['config']['elastic-search']);
        });
        $this->app->singleton('elastic.connection', function (Application $app) {
            return $app['elastic']->connection();
        });
        AliasLoader::getInstance()->alias('ElasticSearch', ElasticSearchManager::class);
        AliasLoader::getInstance()->alias('ElasticSearchIndex', ElasticSearchIndex::class);
    }

}
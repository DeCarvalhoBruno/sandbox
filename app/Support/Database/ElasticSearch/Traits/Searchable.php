<?php namespace App\Support\Database\ElasticSearch;

use App\Support\Database\ElasticSearch\Facades\ElasticSearch;

/**
 * @method static \App\Support\Database\ElasticSearch\DSL\SearchBuilder search()
 * @method static \App\Support\Database\ElasticSearch\DSL\SuggestionBuilder suggest()
 */
trait Searchable
{

    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method == 'search') {
            //Start an elastic dsl search query builder
            return ElasticSearch::search()->model($this);
        }

        if ($method == 'suggest') {
            //Start an elastic dsl suggest query builder
            return ElasticSearch::suggest()->index($this->getDocumentIndex());
        }

        return parent::__call($method, $parameters);
    }
}

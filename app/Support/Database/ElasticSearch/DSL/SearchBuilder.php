<?php namespace App\Support\Database\ElasticSearch\DSL;

use App\Contracts\Searchable;
use App\Support\Database\ElasticSearch\Connection;
use App\Support\Database\ElasticSearch\Exception\InvalidArgumentException;
use App\Support\Database\ElasticSearch\Results\Paginator;
use App\Support\Database\ElasticSearch\Results\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use ONGR\ElasticsearchDSL\Highlight\Highlight;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\CommonTermsQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchPhrasePrefixQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\QueryStringQuery;
use ONGR\ElasticsearchDSL\Query\FullText\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoBoundingBoxQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoPolygonQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoShapeQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\FuzzyQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\IdsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RegexpQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\WildcardQuery;
use ONGR\ElasticsearchDSL\Search as Query;
use ONGR\ElasticsearchDSL\Sort\FieldSort;

class SearchBuilder
{
    use Macroable;

    /**
     * An instance of DSL query.
     *
     * @var Query
     */
    public $query;

    /**
     * The elastic type to query against.
     *
     * @var string
     */
    public $type;

    /**
     * The elastic index to query against.
     *
     * @var string
     */
    public $index;

    /**
     * The model to use when querying elastic search.
     *
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * An instance of plastic Connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Query bool state.
     *
     * @var string
     */
    protected $boolState = BoolQuery::MUST;

    /**
     * Builder constructor.
     *
     * @param \App\Support\Database\ElasticSearch\Connection $connection
     * @param \ONGR\ElasticsearchDSL\Search $grammar
     */
    public function __construct(Connection $connection, Query $grammar)
    {
        $this->connection = $connection;
        $this->query = $grammar;
    }

    /**
     * Set the elastic type to query against.
     *
     * @param string $type
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the elastic index to query against.
     *
     * @param string $index
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function index($index): self
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Set the eloquent model to use when querying elastic search.
     *
     * @param string $model
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function model(string $model): self
    {
        $this->modelClass = $model;
        $modelInstance = new $model;
        if ($modelInstance instanceof Searchable) {
            return $this->type($modelInstance->getDocumentType())->index($modelInstance->getDocumentIndex());
        } else {
            throw new InvalidArgumentException(
                sprintf(
                    'Model %s does not implement the Searchable contract.',
                    $model)
            );
        }
    }

    /**
     * @param array $attributes
     * @return \App\Contracts\Searchable|\Illuminate\Database\Eloquent\Model
     */
    public function createModel(array $attributes = null): Searchable
    {
        return new $this->modelClass($attributes);

    }

    /**
     * Set the query from/offset value.
     *
     * @param int $offset
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function from($offset): self
    {
        $this->query->setFrom($offset);

        return $this;
    }

    /**
     * Set the query limit/size value.
     *
     * @param int $limit
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function size($limit): self
    {
        $this->query->setSize($limit);

        return $this;
    }

    /**
     * Set the query sort values values.
     *
     * @param string|array $fields
     * @param null $order
     * @param array $parameters
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function sortBy($fields, $order = null, array $parameters = []): self
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            $sort = new FieldSort($field, $order, $parameters);

            $this->query->addSort($sort);
        }

        return $this;
    }

    /**
     * Set the query min score value.
     *
     * @param $score
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function minScore($score): self
    {
        $this->query->setMinScore($score);

        return $this;
    }

    /**
     * @param $source
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function setSource($source): self
    {
        $this->query->setSource($source);

        return $this;
    }

    /**
     * Switch to a should statement.
     */
    public function should(): self
    {
        $this->boolState = BoolQuery::SHOULD;

        return $this;
    }

    /**
     * Switch to a must statement.
     */
    public function must(): self
    {
        $this->boolState = BoolQuery::MUST;

        return $this;
    }

    /**
     * Switch to a must not statement.
     */
    public function mustNot(): self
    {
        $this->boolState = BoolQuery::MUST_NOT;

        return $this;
    }

    /**
     * Switch to a filter query.
     */
    public function filter(): self
    {
        $this->boolState = BoolQuery::FILTER;

        return $this;
    }

    /**
     * Add an ids query.
     *
     * @param array | string $ids
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function ids($ids): self
    {
        $ids = is_array($ids) ? $ids : [$ids];

        $query = new IdsQuery($ids);

        $this->append($query);

        return $this;
    }

    /**
     * Add an term query.
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function term($field, $term, array $attributes = []): self
    {
        $query = new TermQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add an terms query.
     *
     * @param string $field
     * @param array $terms
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function terms($field, array $terms, array $attributes = []): self
    {
        $query = new TermsQuery($field, $terms, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add an exists query.
     *
     * @param string|array $fields
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function exists($fields): self
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            $query = new ExistsQuery($field);

            $this->append($query);
        }

        return $this;
    }

    /**
     * Add a wildcard query.
     *
     * @param string $field
     * @param string $value
     * @param float $boost
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function wildcard($field, $value, $boost = 1.0): self
    {
        $query = new WildcardQuery($field, $value, ['boost' => $boost]);

        $this->append($query);

        return $this;
    }

    /**
     * Add a boost query.
     *
     * @param float|null $boost
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     *
     * @internal param $field
     */
    public function matchAll($boost = 1.0): self
    {
        $query = new MatchAllQuery(['boost' => $boost]);

        $this->append($query);

        return $this;
    }

    /**
     * Add a match query.
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function match($field, $term, array $attributes = []): self
    {
        $query = new MatchQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a multi match query.
     *
     * @param array $fields
     * @param string $term
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function multiMatch(array $fields, $term, array $attributes = []): self
    {
        $query = new MultiMatchQuery($fields, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * @param string $field
     * @param string $term
     * @param array $attributes
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function matchPhrasePrefix($field, $term, array $attributes = []): self
    {
        if (empty($attributes)) {
            $attributes = [
                'slop' => 2,
                'max_expansions' => 5
            ];
        }
        $query = new MatchPhrasePrefixQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo bounding box query.
     *
     * @param string $field
     * @param array $values
     * @param array $parameters
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function geoBoundingBox($field, $values, array $parameters = []): self
    {
        $query = new GeoBoundingBoxQuery($field, $values, $parameters);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo distance query.
     *
     * @param string $field
     * @param string $distance
     * @param mixed $location
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function geoDistance($field, $distance, $location, array $attributes = []): self
    {
        $query = new GeoDistanceQuery($field, $distance, $location, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo polygon query.
     *
     * @param string $field
     * @param array $points
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function geoPolygon($field, array $points = [], array $attributes = []): self
    {
        $query = new GeoPolygonQuery($field, $points, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo shape query.
     *
     * @param string $field
     * @param $type
     * @param array $coordinates
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function geoShape($field, $type, array $coordinates = [], array $attributes = []): self
    {
        $query = new GeoShapeQuery();

        $query->addShape($field, $type, $coordinates, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a prefix query.
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function prefix($field, $term, array $attributes = []): self
    {
        $query = new PrefixQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a query string query.
     *
     * @param string $query
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function queryString($query, array $attributes = []): self
    {
        $query = new QueryStringQuery($query, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a simple query string query.
     *
     * @param string $query
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function simpleQueryString($query, array $attributes = []): self
    {
        $query = new SimpleQueryStringQuery($query, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a highlight to result.
     *
     * @param array $fields
     * @param array $parameters
     * @param string $preTag
     * @param string $postTag
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-highlighting.html
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function highlight(
        $fields = ['_all' => []],
        $parameters = [],
        $preTag = '<mark>',
        $postTag = '</mark>'
    ): self {
        $highlight = new Highlight();
        $highlight->setTags([$preTag], [$postTag]);

        foreach ($fields as $field => $fieldParams) {
            $highlight->addField($field, $fieldParams);
        }

        if ($parameters) {
            $highlight->setParameters($parameters);
        }

        $this->query->addHighlight($highlight);

        return $this;
    }

    /**
     * Add a range query.
     *
     * @param string $field
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function range(string $field, array $attributes = []): self
    {
        $query = new RangeQuery($field, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a regexp query.
     *
     * @param string $field
     * @param string $regex
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function regexp(string $field, string $regex, array $attributes = []): self
    {
        $query = new RegexpQuery($field, $regex, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a common term query.
     *
     * @param $field
     * @param $term
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function commonTerm($field, $term, array $attributes = []): self
    {
        $query = new CommonTermsQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a fuzzy query.
     *
     * @param $field
     * @param $term
     * @param array $attributes
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function fuzzy($field, $term, array $attributes = []): self
    {
        $query = new FuzzyQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a nested query.
     *
     * @param $field
     * @param \Closure $closure
     * @param string $score_mode
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function nested($field, \Closure $closure, $score_mode = 'avg'): self
    {
        $builder = new self($this->connection, new $this->query());

        $closure($builder);

        $nestedQuery = $builder->query->getQueries();

        $query = new NestedQuery($field, $nestedQuery, ['score_mode' => $score_mode]);

        $this->append($query);

        return $this;
    }

    /**
     * Add aggregation.
     *
     * @param \Closure $closure
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function aggregate(\Closure $closure): self
    {
        $builder = new AggregationBuilder($this->query);

        $closure($builder);

        return $this;
    }

    /**
     * Add function score.
     *
     * @param \Closure $search
     * @param \Closure $closure
     * @param array $parameters
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function functions(\Closure $search, \Closure $closure, $parameters = []): self
    {
        $builder = new self($this->connection, new $this->query());
        $search($builder);

        $builder = new FunctionScoreBuilder($builder, $parameters);

        $closure($builder);

        $this->append($builder->getQuery());

        return $this;
    }

    /**
     * Execute the search query against elastic and return the raw result.
     *
     * @return array
     */
    public function getRaw()
    {
        $params = [
            'index' => $this->getIndex(),
            'type' => $this->getType(),
            'body' => $this->toDSL(),
        ];

        return $this->connection->searchStatement($params);
    }

    /**
     *
     * @return \App\Support\Database\ElasticSearch\Results\SearchResult
     */
    public function get(): SearchResult
    {
        return new SearchResult($this->getRaw());
    }

    /**
     * @return \App\Support\Database\ElasticSearch\Results\SearchResult
     */
    public function toModel(): SearchResult
    {
        return SearchResult::toModel($this->getRaw(), $this->modelClass);
    }

    /**
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the current elastic index.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Return the current plastic connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Return the boolean query state.
     *
     * @return string
     */
    public function getBoolState()
    {
        return $this->boolState;
    }

    /**
     * Paginate result hits.
     *
     * @param int $limit
     *
     * @param bool $toModel
     * @return \App\Support\Database\ElasticSearch\Results\Paginator
     */
    public function paginate($limit = 25, $toModel = true): Paginator
    {
        $page = $this->getCurrentPage();

        $this->from($limit * ($page - 1))->size($limit);

        if ($toModel) {
            return new Paginator($this->toModel(), $limit, $page);
        }

        return new Paginator($this->get(), $limit, $page);
    }

    /**
     * Return the DSL query.
     *
     * @return array
     */
    public function toDSL()
    {
        return $this->query->toArray();
    }

    /**
     * Append a query.
     *
     * @param $query
     *
     * @return \App\Support\Database\ElasticSearch\DSL\SearchBuilder
     */
    public function append($query)
    {
        $this->query->addQuery($query, $this->getBoolState());

        return $this;
    }

    /**
     * return the current query string value.
     *
     * @return int
     */
    protected function getCurrentPage()
    {
        return \Request::get('page') ? (int)\Request::get('page') : 1;
    }
}

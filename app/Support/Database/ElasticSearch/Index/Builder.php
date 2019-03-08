<?php namespace App\Support\Database\ElasticSearch\Index;

use App\Support\Database\ElasticSearch\Connection;
use App\Support\Database\ElasticSearch\Results\IndexingResult;

/**
 * Builder for
 */
class Builder
{

    /**
     * Plastic connection instance.
     *
     * @var \App\Support\Database\ElasticSearch\Connection
     */
    protected $connection;

    /**
     * Schema constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param array $params
     * @return array
     */
    public function create($params): array
    {
        return $this->connection->indicesCreateStatement($params);
    }

    /**
     * @param array $params
     * @return array
     */
    public function delete($params): array
    {
        return $this->connection->indicesDeleteStatement($params);
    }

    /**
     * @param array $params
     * @return IndexingResult
     */
    public function bulk($params): IndexingResult
    {
        return new IndexingResult($this->connection->bulkStatement($params));
    }

}
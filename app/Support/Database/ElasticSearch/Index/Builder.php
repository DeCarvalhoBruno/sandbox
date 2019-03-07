<?php namespace App\Support\Database\ElasticSearch\Index;

use App\Support\Database\ElasticSearch\Connection;

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

    public function create($params)
    {
        return $this->connection->indicesStatement($params);
    }

}
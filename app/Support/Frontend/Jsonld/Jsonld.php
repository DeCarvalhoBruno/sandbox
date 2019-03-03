<?php

namespace App\Support\Frontend\Jsonld;

class JsonLd
{
    /**
     * Context type
     *
     * @var \App\Support\Frontend\Jsonld\Schema
     */
    protected $schema = null;

    /**
     * Create a new Context instance
     *
     * @param string $schema
     * @param array $data
     */
    public function __construct($schema, array $data = [])
    {
        $this->schema = new $schema($data, true);
    }

    /**
     * Present given data as a JSON-LD object.
     *
     * @param string $context
     * @param array $data
     *
     * @return static
     */
    public static function create($context, array $data = [])
    {
        return new static($context, $data);
    }

    /**
     * Generate the JSON-LD script tag.
     *
     * @return string
     */
    public function generate()
    {
        $properties = $this->schema->getProperties();
        return $properties ? "<script type=\"application/ld+json\">" . json_encode($properties,
                JSON_HEX_APOS | JSON_UNESCAPED_UNICODE) . "</script>" : '';
    }

    /**
     * Return script tag.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }
}

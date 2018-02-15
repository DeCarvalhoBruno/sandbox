<?php namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    /**
     * @var array
     */
    protected $filters = [];

    /**
     * @var \Illuminate\Database\Query\Builder
     */
    protected $builder;

    /**
     * @var array
     */
    private $parsedFilters;

    /**
     * @var array
     */
    protected $acceptedSortColumns;


    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(Request $request)
    {
        $this->parsedFilters = $request->only($this->filters);
        $this->acceptedSortColumns = array_flip($this->acceptedSortColumns);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $builder
     * @return \Illuminate\Database\Query\Builder
     */
    public function apply($builder)
    {
        $this->builder = $builder;
        foreach ($this->parsedFilters as $filter => $value) {
            if (method_exists($this, $filter)) {
                $this->$filter($value);
            }
        }
        return $builder;
    }

    public function getFilter($value)
    {
        return $this->parsedFilters[$value] ?? null;
    }
}
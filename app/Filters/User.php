<?php namespace App\Filters;

class User extends Filters
{
    protected $filters = ['sortBy', 'order', 'name', 'createdAt', 'group'];

    protected $acceptedSortColumns = ['full_name', 'email', 'created_at'];

    /**
     * @param string $name
     * @return \Illuminate\Database\Query\Builder
     */
    public function sortBy($name)
    {
        if (isset($this->acceptedSortColumns[trans(sprintf('ajax.db_raw.%s', $name))])) {
            return $this->builder
                ->orderBy($name,
                    trans(
                        sprintf('ajax.filters.%s',
                            $this->getFilter('order')
                        )
                    ) ?? 'asc'
                );
        }
        return $this->builder;
    }

    /**
     * @param string $name
     * @return \Illuminate\Database\Query\Builder
     */
    public function name($name)
    {
        return $this->builder->where(
            trans(sprintf('ajax.db_raw.%s', 'full_name')),
            'like',
            sprintf('%%%s%%', $name));
    }

    /**
     * @param string $name
     * @return \Illuminate\Database\Query\Builder
     */
    public function group($name)
    {
        return $this->builder->groupMember($name);
    }

    /**
     * @param string $date
     * @return \Illuminate\Database\Query\Builder
     */
    public function createdAt($date)
    {
        return $this->builder;
    }
}
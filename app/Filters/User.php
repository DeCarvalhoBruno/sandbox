<?php namespace App\Filters;

class User extends Filters
{
    protected $filters = ['sortByCol', 'order', 'name'];
    protected $acceptedSortColumns = ['full_name', 'email', 'created_at'];

    public function sortByCol($name)
    {
        if (isset($this->acceptedSortColumns[$name])) {
            return $this->builder->orderBy($name, $this->getFilter('order') ?? 'asc');
        }
        return $this->builder;
    }

    public function name($name)
    {
        return $this->builder->where('full_name', 'like', sprintf('%%%s%%', $name));
    }

}
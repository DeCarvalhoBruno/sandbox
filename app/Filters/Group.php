<?php namespace App\Filters;

class Group extends Filters
{
    protected $filters = ['sortByCol', 'order', 'name'];
    protected $acceptedSortColumns = ['group_name'];

    public function sortByCol($name)
    {
        if (isset($this->acceptedSortColumns[$name])) {
            return $this->builder->orderBy($name, $this->getFilter('order') ?? 'asc');
        }
        return $this->builder;
    }

    public function name($name)
    {
        return $this->builder->where('group_name', 'like', sprintf('%%%s%%', $name));
    }

}
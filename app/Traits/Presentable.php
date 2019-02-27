<?php namespace App\Traits;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;

trait Presentable
{
    /**
     * @param $columns
     * @param $filter \App\Filters\Filters
     * @return array
     */
    public function getColumnInfo($columns, $filter): array
    {
        $sortable = array_flip($this->sortable);
        $result = [];
        foreach ($columns as $name => $info) {
            $result[$name] = [
                'name' => $name,
                'width' => (isset($info->width)) ? $info->width : 'inherit',
                'label' => $info->name,
                'sortable' => isset($sortable[$name])
            ];
            if ($name === $filter->getFilter(trans('js-backend.filters.sortBy'))) {
                $result[$name]['order'] = $filter->getFilter(trans('js-backend.filters.order'));
            }
        }
        return $result;
    }

    /**
     * @param mixed $query
     * @param \App\Filters\Filters $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, Filters $filters): Builder
    {
        return $filters->apply($query);
    }

}
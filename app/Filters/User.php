<?php namespace App\Filters;

use Carbon\Carbon;

class User extends Filters
{
    protected $filters = ['sortBy', 'order', 'fullName', 'createdAt', 'group'];

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
    public function fullName($name)
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
        switch ($date) {
            case trans("ajax.filters_inv.week"):
                $testedDate = Carbon::now()->subWeek()->toDateTimeString();
                break;
            case trans("ajax.filters_inv.month"):
                $testedDate = Carbon::now()->subMonth()->toDateTimeString();
                break;
            case trans("ajax.filters_inv.year"):
                $testedDate = Carbon::now()->subYear()->toDateTimeString();
                break;
            default:
                $testedDate = Carbon::now()->setTime(0, 0, 0)->toDateTimeString();
        }
        return $this->builder->where(
            'people.created_at',
            '>',
            $testedDate);
    }
}
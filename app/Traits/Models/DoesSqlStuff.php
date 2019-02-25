<?php

namespace App\Traits\Models;

use Illuminate\Database\Eloquent\Builder;

trait DoesSqlStuff
{
    /**
     * Join a table using the source table's primary key
     * Example : join(person) on user will use person.user_id to join user on user.user_id
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $modelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function join($builder, $modelName)
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelToJoin */
        $modelToJoin = new $modelName;
        return $builder->join(
            $modelToJoin->getTable(),
            $this->getQualifiedKeyName(),
            '=',
            sprintf('%s.%s', $modelToJoin->getTable(), $this->getKeyName())
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $modelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function leftJoin($builder, $modelName)
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelToJoin */
        $modelToJoin = new $modelName;
        return $builder->leftJoin(
            $modelToJoin->getTable(),
            $this->getQualifiedKeyName(),
            '=',
            sprintf('%s.%s', $modelToJoin->getTable(), $this->getKeyName())
        );
    }

    /**
     *  Join a table using the joined table's primary key
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $modelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function joinReverse(Builder $builder, $modelName): Builder
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelToJoin */
        $modelToJoin = new $modelName;
        return $builder->join(
            $modelToJoin->getTable(),
            $modelToJoin->getQualifiedKeyName(),
            '=',
            sprintf('%s.%s', $this->getTable(), $modelToJoin->getKeyName())
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $modelName
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function joinReverseWithSlug(Builder $builder, $modelName, $slug = null)
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelToJoin */
        $modelToJoin = new $modelName;

        return $builder->join(
            $modelToJoin->getTable(),
            function ($q) use ($slug, $modelName, $modelToJoin) {
                $q->on(
                    $modelToJoin->getQualifiedKeyName(),
                    '=',
                    sprintf('%s.%s', $this->getTable(), $modelToJoin->getKeyName())
                );
                if (!is_null($slug)) {
                    $q->where($modelName::$slugColumn,
                        '=', $slug);
                }
            });
    }

    public function joinWithWheres($builder, $modelName, $wheres = null): Builder
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelToJoin */
        $modelToJoin = new $modelName;

        return $builder->join(
            $modelToJoin->getTable(),
            function ($q) use ($modelName, $modelToJoin, $wheres) {
                $q->on(
                    $this->getQualifiedKeyName(),
                    '=',
                    sprintf('%s.%s', $modelToJoin->getTable(), $this->getKeyName())
                );
                if (!is_null($wheres)) {
                    foreach ($wheres as $whereKey => $whereValue) {
                        $q->where($whereKey, $whereValue);
                    }
                }
            });

    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $modelName
     * @param string $key
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function joinWithKey($builder, $modelName, $key)
    {
        /** @var \Illuminate\Database\Eloquent\Model $modelToJoin */
        $modelToJoin = new $modelName;
        return $builder->join(
            $modelToJoin->getTable(),
            sprintf('%s.%s', $modelToJoin->getTable(), $key),
            '=',
            sprintf('%s.%s', $this->getTable(), $key)
        );
    }


}

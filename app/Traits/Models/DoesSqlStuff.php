<?php

namespace App\Traits\Models;

trait DoesSqlStuff
{
    /**
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
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param string $modelName
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function joinReverse($builder, $modelName)
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

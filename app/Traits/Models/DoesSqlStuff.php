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
}

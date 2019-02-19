<?php

namespace App\Traits\Models;

trait HasASlugColumn
{
    /**
     * Get the name of the column in the entity's table that gives it its name, i.e name, title, label, etc.
     *
     * @see \App\Models\Entity
     * @return string
     */
    public function getSlugColumn(){
        return $this->getAttribute(static::$slugColumn);
    }

    public function getSlugColumnName()
    {
        return static::$slugColumn;
    }

}

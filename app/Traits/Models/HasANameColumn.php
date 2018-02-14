<?php

namespace App\Traits\Models;

trait HasANameColumn
{
    /**
     * Get the name of the column in the entity's table that gives it its name, i.e name, title, label, etc.
     *
     * @see \App\Models\SystemEntity
     * @return string
     */
    public function getNameColumn(){
        return $this->getAttribute(static::$nameColumn);
    }
}

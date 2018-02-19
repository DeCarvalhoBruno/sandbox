<?php

namespace App\Traits\Models;

trait HasAnEntity
{
    /**
     * Classes using this trait should define a entityID
     * whose value matches a record in the entities table
     *
     * @see \App\Models\Entity
     * @return int
     */
    public function getEntity(){
        return $this->entityID;
    }

}

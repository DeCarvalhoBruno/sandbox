<?php

namespace App\Contracts;

interface HasAnEntity
{
    /**
     * Classes using this trait should define a entityID
     * whose value matches a record in the entities table
     *
     * @see \App\Models\Entity
     * @return int
     */
    public function getEntity();

//    public function deleteWithMedia($targetID,$entityID);
}

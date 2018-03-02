<?php namespace App\Contracts;

interface RawQueries
{
    public function getUsersInArrayNotInGroup($testedArray, $group);

    public function triggerCreateEntityType($name, $primaryKey);

    public function triggerDeleteEntityType($name, $primaryKey);

    public function triggerUserFullName();
}
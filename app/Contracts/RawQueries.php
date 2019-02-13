<?php namespace App\Contracts;

/**
 * @see \App\Support\Database\RawQueries
 */
interface RawQueries
{
    public function getUsersInArrayNotInGroup($testedArray, $group);

    public function triggerCreateEntityType($name, $primaryKey);

    public function triggerCreateEntityTypeUsers();

    public function triggerDeleteEntityType($name, $primaryKey, $entityTypeId);

    public function triggerUserFullName();

    public function getAllUserPermissions($entityTypeId);

}
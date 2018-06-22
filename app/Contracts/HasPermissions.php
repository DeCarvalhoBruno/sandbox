<?php namespace App\Contracts;

interface HasPermissions
{
    public function getReadablePermissions($value = 65535, $toArray = false);

}
<?php namespace App\Contracts;

interface Image
{
    public function getUuid();

    public function getTargetType();

    public function getTargetSlug();

}
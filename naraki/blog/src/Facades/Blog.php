<?php namespace Naraki\Blog\Facades;

use Naraki\Blog\Contracts\Blog as BlogContract;
use Illuminate\Support\Facades\Facade;

class Blog extends Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return BlogContract::class;
    }
}
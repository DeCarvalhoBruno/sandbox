<?php namespace Naraki\Blog\Facades;

use Naraki\Blog\Providers\Blog as BlogProvider;

class Blog extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return BlogProvider::class;
    }
}
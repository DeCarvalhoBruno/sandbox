<?php namespace Naraki\Blog\Facades;

class BlogFacade extends \Illuminate\Support\Facades\Facade
{
    /**
     * {@inheritDoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Naraki\Blog\Providers\Blog::class;
    }
}
<?php namespace App\Support\Providers;

use App\Contracts\Models\Blog as BlogInterface;

class Blog extends Model implements BlogInterface
{
    protected $model = \App\Models\Blog\BlogPost::class;

    public function getOneBySlug($slug,$attributes)
    {
        return $this->createModel()->newQuery()
            ->select($attributes)
            ->status()
            ->where('blog_post_slug','=',$slug);

    }

    public function updateOne()
    {


    }

}
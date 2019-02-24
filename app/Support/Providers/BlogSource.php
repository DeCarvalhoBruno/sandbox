<?php namespace App\Support\Providers;

use App\Contracts\Models\BlogSource as BlogSourceInterface;

class BlogSource extends Model implements BlogSourceInterface
{
    protected $model = \App\Models\Blog\BlogSourceRecord::class;

    public function buildByBlogSlug($slug, $columns = ['*'])
    {
        return $this->createModel()->newQuery()->select($columns)
            ->post($slug)->recordType();
    }

}
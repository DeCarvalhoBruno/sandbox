<?php namespace App\Support\Providers;

use App\Contracts\Models\BlogSource as BlogSourceInterface;
use App\Models\Blog\BlogSourceRecordType;

class BlogSource extends Model implements BlogSourceInterface
{
    protected $model = \App\Models\Blog\BlogSourceRecord::class;

    public function buildByBlogSlug($slug, $columns = ['*'])
    {
        return $this->createModel()->newQuery()->select($columns)
            ->post($slug)->recordType();
    }

    public static function listTypes()
    {
        return BlogSourceRecordType::getPresentableConstants();
    }

}
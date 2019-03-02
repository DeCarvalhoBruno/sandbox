<?php namespace App\Support\Providers;

use App\Contracts\Models\BlogSource as BlogSourceInterface;
use App\Models\Blog\BlogSource as BlogSourceModel;
use App\Models\Blog\BlogSourceRecord;

class BlogSource extends Model implements BlogSourceInterface
{
    protected $model = \App\Models\Blog\BlogSourceRecord::class;

    public function buildByBlogSlug($slug, $columns = null)
    {
        if (is_null($columns)) {
            $columns = [
                'blog_source_content as source',
                'blog_source_name as type',
                'blog_source_record_id as record'
            ];
        }
        return $this->createModel()->newQuery()->select($columns)
            ->post($slug)->source();
    }

    public function createOne($type, $content, $blogSlug)
    {
        $blogPost = $this->createModel()->newQuery()->select(['blog_posts.blog_post_id'])
            ->post($blogSlug)->first();
        if (!is_null($blogPost)) {
            BlogSourceRecord::create([
                'blog_post_id' => $blogPost->getAttribute('blog_post_id'),
                'blog_source_id' => $type,
                'blog_source_content' => $content
            ]);
        }
    }

    public function deleteOne($id)
    {
        $model = $this->createModel()->newQuery()->select(['blog_source_record_id'])
            ->where('blog_source_record_id', $id)->first();
        if (!is_null($model)) {
            return $model->delete();
        }
        return false;
    }

    public static function listTypes()
    {
        return BlogSourceModel::getPresentableConstants();
    }

}
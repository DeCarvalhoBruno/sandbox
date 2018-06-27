<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Filters\Blog as BlogFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateBlogPost;
use App\Models\Blog\BlogPost;
use App\Models\Blog\BlogPostStatus;
use App\Contracts\Models\Blog as BlogProvider;

class Blog extends Controller
{
    /**
     * @param \App\Filters\Blog $filter
     * @return array
     */
    public function index(BlogFilter $filter)
    {
        return [
            'table' => \App\Models\Blog\BlogPost::query()
                ->select([
                    'blog_post_title as ' . trans('ajax.db_raw_inv.blog_post_title'),
                    'blog_post_slug'
                ])
                ->filter($filter)->paginate(10),
            'columns' => (new \App\Models\Group)->getColumnInfo([
                trans('ajax.db_raw_inv.blog_post_title') => trans('ajax.db.blog_post_title')
            ])
        ];
    }

    public function add()
    {
        return [
            'record' => [
                'blog_post_status' => BlogPostStatus::getConstantByID(BlogPostStatus::BLOG_POST_STATUS_DRAFT)
            ],
            'status_list' => BlogPostStatus::getConstants('BLOG'),
        ];
    }

    public function edit($slug, BlogProvider $blogRepo)
    {
        return [
            'record' =>
                $blogRepo->getOneBySlug(
                    $slug,
                    [
                        'blog_post_title',
                        'blog_post_content',
                        'blog_post_excerpt',
                        'blog_post_status_name as blog_post_status'
                    ])->first(),
            'status_list' => BlogPostStatus::getConstants('BLOG'),
        ];
    }

    public function create(CreateBlogPost $request)
    {
        $post = new BlogPost($request->all());
        $post->save();
        $params = [
            'slug' => $post->getAttribute('blog_post_slug'),
        ];
        if ($post->getAttribute('blog_post_status_id') != BlogPostStatus::BLOG_POST_STATUS_PUBLISHED) {
            $params['preview'] = true;
        }
        return (
        [
            'blog_post_slug' => route_i18n('blog', $params)
        ]);
    }

    public function update($slug,CreateBlogPost $request,BlogProvider $blogRepo)
    {
        $blogRepo->updateOne();
        dd($request->all());

    }

}
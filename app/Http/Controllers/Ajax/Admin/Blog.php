<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Blog as BlogProvider;
use App\Filters\Blog as BlogFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateBlogPost;
use App\Models\Blog\BlogPostStatus;
use App\Support\Providers\User as UserProvider;

class Blog extends Controller
{
    /**
     * @param \App\Filters\Blog $filter
     * @return array
     */
    public function index(BlogFilter $filter, BlogProvider $blogRepo)
    {
        return [
            'table' => $blogRepo->buildList([
                'blog_post_title as ' . trans('ajax.db_raw_inv.blog_post_title'),
                'username as ' . trans('ajax.db_raw_inv.username'),
                'blog_post_slug'
            ])->filter($filter)->paginate(10),
            'columns' => (new \App\Models\Group)->getColumnInfo([
                trans('ajax.db_raw_inv.blog_post_title') => trans('ajax.db.blog_post_title'),
                trans('ajax.db_raw_inv.username') => trans('ajax.db.username')
            ])
        ];
    }

    public function add()
    {
        return [
            'record' => [
                'blog_post_status' => BlogPostStatus::getConstantByID(BlogPostStatus::BLOG_POST_STATUS_DRAFT),
                'blog_post_user' => auth()->user()->getAttribute('username')
            ],
            'status_list' => BlogPostStatus::getConstants('BLOG'),
        ];
    }

    public function edit($slug, BlogProvider $blogRepo)
    {
        return [
            'record' =>
                $blogRepo->buildOneBySlug(
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

    public function create(CreateBlogPost $request, BlogProvider $blogRepo, UserProvider $userRepo)
    {
        $post = $blogRepo->createOne(
            $request->all(),
            $userRepo->buildOneByUsername(
                $request->getUsername(),
                [$userRepo->getQualifiedKeyName()]
            )
        );
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

    public function update($slug, CreateBlogPost $request, BlogProvider $blogRepo)
    {
        $blogRepo->updateOne($slug, $request->all());
    }

}
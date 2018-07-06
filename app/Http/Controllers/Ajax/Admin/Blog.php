<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Blog as BlogProvider;
use App\Filters\Blog as BlogFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateBlogPost;
use App\Models\Blog\BlogPostStatus;
use App\Contracts\Models\User as UserProvider;
use App\Contracts\Models\Media as MediaProvider;

class Blog extends Controller
{
    /**
     * @param \App\Filters\Blog $filter
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     * @return array
     */
    public function index(BlogFilter $filter, BlogProvider $blogRepo)
    {
        return [
            'table' => $blogRepo->buildList([
                'blog_post_title',
                'username',
                'blog_post_slug'
            ])->filter($filter)->paginate(10),
            'columns' => $blogRepo->createModel()->getColumnInfo([
                'blog_post_title' => (object)[
                    'name' => trans('ajax.db.blog_post_title'),
                    'width' => '50%'
                ],
                'username' => (object)[
                    'name' => trans('ajax.db.username'),
                    'width' => '40%'
                ]
            ])
        ];
    }

    /**
     * @return array
     */
    public function add()
    {
        return [
            'record' => [
                'blog_post_status' => BlogPostStatus::getConstantByID(BlogPostStatus::BLOG_POST_STATUS_DRAFT),
                'blog_post_user' => auth()->user()->getAttribute('username'),
                'categories' => [],
                'tags' => []
            ],
            'status_list' => BlogPostStatus::getConstants('BLOG'),
            'blog_post_categories' => \App\Support\Trees\BlogPostCategory::getTree()
        ];
    }

    /**
     * @param $slug
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     * @return array
     */
    public function edit($slug, BlogProvider $blogRepo, MediaProvider $mediaRepo)
    {
        $record = $blogRepo->buildOneBySlug(
            $slug,
            [
                'blog_post_id',
                'blog_post_title',
                'blog_post_slug',
                'blog_post_content',
                'blog_post_excerpt',
                'blog_post_status_name as blog_post_status',
                'users.username as blog_post_user'
            ])->first();
        if (is_null($record)) {
            return null;
        }
        $blogPost = $record->toArray();
        $categories = \App\Support\Trees\BlogPostCategory::getTreeWithSelected($blogPost['blog_post_id']);
        $blogPost['categories'] = $categories->categories;
        $blogPost['tags'] = $blogRepo->tag()->getByPost($blogPost['blog_post_id']);
        return [
            'record' => $blogPost,
            'status_list' => BlogPostStatus::getConstants('BLOG'),
            'blog_post_categories' => $categories->tree,
            'blog_post_slug' => $this->getPostUrl($record),
            'thumbnails'=>$mediaRepo->image()->getImages(
                $record->getAttribute('entity_type_id'), [
                'media_uuid as uuid',
                'media_extension as ext'
            ])
        ];
    }

    /**
     * @param \App\Http\Requests\Admin\CreateBlogPost $request
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return array
     */
    public function create(CreateBlogPost $request, BlogProvider $blogRepo, UserProvider $userRepo)
    {
        $post = $blogRepo->createOne(
            $request->all(),
            $userRepo->buildOneByUsername(
                $request->getUsername(),
                [$userRepo->getQualifiedKeyName()]
            )
        );

        $blogRepo->category()->attachToPost($request->getCategories(), $post);
        $blogRepo->tag()->attachToPost($request->getTags(), $post);

        return (
        [
            'blog_post_slug' => $this->getPostUrl($post)
        ]);
    }

    private function getPostUrl($post)
    {
        $params = [
            'slug' => $post->getAttribute('blog_post_slug'),
        ];
        if ($post->getAttribute('blog_post_status_id') != BlogPostStatus::BLOG_POST_STATUS_PUBLISHED) {
            $params['preview'] = true;
        }
        return route_i18n('blog', $params);
    }

    /**
     * @param $slug
     * @param \App\Http\Requests\Admin\CreateBlogPost $request
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     */
    public function update($slug, CreateBlogPost $request, BlogProvider $blogRepo)
    {
        $post = $blogRepo->updateOne($slug, $request->all());
        $blogRepo->category()->updatePost($request->getCategories(), $post);
        $blogRepo->tag()->updatePost($request->getTags(), $post);
    }

}
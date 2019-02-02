<?php namespace App\Http\Controllers\Ajax\Admin;

use App\Contracts\Models\Blog as BlogProvider;
use App\Contracts\Models\Media as MediaProvider;
use App\Contracts\Models\User as UserProvider;
use App\Filters\Blog as BlogFilter;
use App\Http\Controllers\Admin\Controller;
use App\Http\Requests\Admin\CreateBlogPost;
use App\Models\Blog\BlogPostStatus;
use App\Models\Entity;
use App\Models\Media\MediaImgFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
                    'width' => '30%'
                ]
            ], $filter)
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
                'blog_post_user' => $this->user->getAttribute('username'),
                'categories' => [],
                'tags' => [],
            ],
            'status_list' => BlogPostStatus::getConstants('BLOG'),
            'blog_post_categories' => \App\Support\Trees\BlogPostCategory::getTree(),
            'thumbnails' => []
        ];
    }

    /**
     * @param $slug
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
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
                'published_at',
                'blog_post_status_name as blog_post_status',
                'users.username as blog_post_user',
                'entity_type_id'
            ])->first();
        if (is_null($record)) {
            return response(trans('error.http.500.blog_post_not_found'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $blogPost = $record->toArray();
        $categories = \App\Support\Trees\BlogPostCategory::getTreeWithSelected($blogPost['blog_post_id']);
        $blogPost['categories'] = $categories->categories;
        $blogPost['tags'] = $blogRepo->tag()->getByPost($blogPost['blog_post_id']);
        unset($blogPost['entity_type_id'], $blogPost['blog_post_id']);
        return [
            'record' => $blogPost,
            'status_list' => BlogPostStatus::getConstants('BLOG'),
            'blog_post_categories' => $categories->tree,
            'url' => $this->getPostUrl($record),
            'blog_post_slug' => $record->getAttribute('blog_post_slug'),
            'thumbnails' => $mediaRepo->image()->getImages(
                $record->getAttribute('entity_type_id'), [
                'media_uuid as uuid',
                'media_in_use as used',
                'media_extension as ext',
                \DB::raw(
                    sprintf(
                        '"%s" as suffix',
                        MediaImgFormat::getFormatAcronyms(MediaImgFormat::THUMBNAIL)
                    )
                ),

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
            'url' => $this->getPostUrl($post),
            'blog_post_slug' => $post->getAttribute('blog_post_slug'),
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
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     */
    public function update($slug, CreateBlogPost $request, BlogProvider $blogRepo, UserProvider $userRepo)
    {
        $user = $request->getUsername();

        if (!is_null($user)) {
            $query = $userRepo->buildOneByUsername(
                $user,
                [$userRepo->getQualifiedKeyName()]
            )->get();
            if (!is_null($query)) {
                $request->setUserId($query->pluck($userRepo->getKeyName())->first());
            }
        }
        $post = $blogRepo->updateOne($slug, $request->all());
        $blogRepo->category()->updatePost($request->getCategories(), $post);
        $blogRepo->tag()->updatePost($request->getTags(), $post);
    }

    /**
     * @param $slug
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function destroy($slug, BlogProvider $blogRepo, MediaProvider $mediaRepo)
    {
        try {
            $mediaUuids = $mediaRepo->image()
                ->getImagesFromSlug(
                    $slug,
                    Entity::BLOG_POSTS,
                    ['media_uuid']
                )->pluck('media_uuid')->all();
            \DB::transaction(function () use ($slug, $blogRepo, $mediaRepo, $mediaUuids) {
                $mediaRepo->image()->delete($mediaUuids, Entity::BLOG_POSTS);
                $blogRepo->deleteBySlug($slug);
            });
        } catch (\Exception $e) {
            return response(trans('error.http.500.general_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function batchDestroy(BlogProvider $blogRepo, MediaProvider $mediaRepo, Request $request)
    {
        $postSlugs = $request->only('posts');
        $blogRepo->deleteBySlug($request->only('posts'));
        $mediaRepo->image()->getImagesFromSlug($postSlugs);

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param $slug
     * @param $uuid
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return mixed
     */
    public function setFeaturedImage($slug, $uuid, MediaProvider $mediaRepo)
    {
        $mediaRepo->image()->setAsUsed($uuid);
        $media = $mediaRepo->image()->getOne($uuid, ['media_extension']);
        if (!is_null($media)) {
            $mediaRepo->image()->cropImageToFormat(
                $uuid,
                Entity::BLOG_POSTS,
                \App\Models\Media\Media::IMAGE,
                $media->getAttribute('media_extension'),
                MediaImgFormat::FEATURED
            );
        }

        return $mediaRepo->image()->getImagesFromSlug($slug, Entity::BLOG_POSTS)->toArray();
    }

    /**
     * @param string $slug
     * @param string $uuid
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return mixed
     */
    public function deleteImage($slug, $uuid, MediaProvider $mediaRepo)
    {
        $mediaRepo->image()->delete(
            $uuid,
            Entity::BLOG_POSTS);
        return $mediaRepo->image()->getImagesFromSlug($slug, Entity::BLOG_POSTS)->toArray();
    }

}
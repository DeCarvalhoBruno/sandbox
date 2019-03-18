<?php namespace Naraki\Blog\Controllers\Ajax;

use App\Contracts\Models\User as UserProvider;
use App\Http\Controllers\Admin\Controller;
use App\Models\Entity;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Naraki\Blog\Contracts\Blog as BlogProvider;
use Naraki\Blog\Filters\Blog as BlogFilter;
use Naraki\Blog\Job\UpdateElasticsearch;
use Naraki\Blog\Models\BlogStatus;
use Naraki\Blog\Requests\CreateBlogPost;
use Naraki\Blog\Requests\UpdateBlogPost;
use Naraki\Media\Contracts\Media as MediaProvider;
use Naraki\Media\Models\MediaImgFormat;

class Blog extends Controller
{
    use DispatchesJobs;

    /**
     * @param \Naraki\Blog\Filters\Blog $filter
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @return array
     */
    public function index(BlogFilter $filter, BlogProvider $blogRepo): array
    {
        return [
            'table' => $blogRepo->buildList([
                \DB::raw('null as selected'),
                'blog_post_title',
                'full_name',
                'blog_post_slug'
            ])->filter($filter)->paginate(25),
            'columns' => $blogRepo->createModel()->getColumnInfo([
                'blog_post_title' => (object)[
                    'name' => trans('js-backend.db.blog_post_title'),
                    'width' => '50%'
                ],
                'full_name' => (object)[
                    'name' => trans('js-backend.db.full_name'),
                    'width' => '30%'
                ]
            ], $filter)
        ];
    }

    /**
     * @return array
     */
    public function add(): array
    {
        return [
            'record' => [
                'blog_status' => BlogStatus::getConstantByID(BlogStatus::BLOG_STATUS_DRAFT),
                'blog_post_person' => $this->user->getAttribute('full_name'),
                'person_slug' => $this->user->getAttribute('person_slug'),
                'categories' => [],
                'tags' => [],
            ],
            'status_list' => BlogStatus::getConstants('BLOG'),
            'blog_categories' => \Naraki\Blog\Support\Trees\Category::getTree(),
            'thumbnails' => []
        ];
    }

    /**
     * @param $slug
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return array
     */
    public function edit($slug, BlogProvider $blogRepo, MediaProvider $mediaRepo): array
    {
        $record = $blogRepo->buildOneBySlug(
            $slug,
            [
                'blog_posts.blog_post_id',
                'blog_post_title',
                'blog_post_slug',
                'blog_post_content',
                'blog_post_excerpt',
                'published_at',
                'blog_posts.blog_status_id',
                'blog_status_name as blog_status',
                'people.full_name as blog_post_person',
                'entity_type_id'
            ])->first();
        if (is_null($record)) {
            return response(trans('error.http.500.blog_post_not_found'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $blogPost = $record->toArray();
        $categories = \Naraki\Blog\Support\Trees\Category::getTreeWithSelected($blogPost['blog_post_id']);
        $blogPost['categories'] = $categories->categories;
        $blogPost['tags'] = $blogRepo->tag()->getByPost($blogPost['blog_post_id']);
        unset($blogPost['entity_type_id'], $blogPost['blog_post_id']);
        return [
            'record' => $blogPost,
            'status_list' => BlogStatus::getConstants('BLOG'),
            'blog_categories' => $categories->tree,
            'url' => $this->getPostUrl($record),
            'source_types' => $blogRepo->source()->listTypes(),
            'sources' => $blogRepo->source()
                ->buildByBlogSlug(
                    $record->getAttribute('blog_post_slug')
                )->get()->toArray(),
            'blog_post_slug' => $record->getAttribute('blog_post_slug'),
            'thumbnails' => $mediaRepo->image()->getImages(
                $record->getAttribute('entity_type_id'))
        ];
    }

    /**
     * @param \Naraki\Blog\Requests\CreateBlogPost $request
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return array
     */
    public function create(CreateBlogPost $request, BlogProvider $blogRepo, UserProvider $userRepo): array
    {
        try {
            $post = $blogRepo->createOne(
                $request->all(),
                $userRepo->person()->buildOneBySlug(
                    $request->getPersonSlug(),
                    [$userRepo->person()->getKeyName()]
                )
            );

            $blogRepo->category()->attachToPost($request->getCategories(), $post);
            $blogRepo->tag()->attachToPost($request->getTags(), $post);
        } catch (\Exception $e) {
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return (
        [
            'url' => $this->getPostUrl($post),
            'blog_post_slug' => $post->getAttribute('blog_post_slug'),
        ]);
    }

    /**
     * @param $slug
     * @param \Naraki\Blog\Requests\UpdateBlogPost $request
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return array
     */
    public function update($slug, UpdateBlogPost $request, BlogProvider $blogRepo, UserProvider $userRepo): array
    {
        $person = $request->getPersonSlug();

        if (!is_null($person)) {
            $query = $userRepo->person()->buildOneBySlug(
                $person,
                [$userRepo->person()->getKeyName()]
            )->first();
            if (!is_null($query)) {
                $request->setPersonSlug($query[$userRepo->person()->getKeyName()]);
            }
        }
        $post = $blogRepo->updateOne($slug, $request->all());
        $blogRepo->category()->updatePost($request->getCategories(), $post);
        $blogRepo->tag()->updatePost($request->getTags(), $post);
//        $this->dispatch(
//            new UpdateElasticsearch(
//                $post->only(['system_entity_id', 'blog_post_id']),
//                $request->all(),
//                $request->getCategories(),
//                $request->getTags()
//            )
//        );
        return [
            'post'=>$post->only(['system_entity_id', 'blog_post_id']),
            'request'=>$request->all(),
            'cats'=>$request->getCategories(),
            'tags'=>$request->getTags(),
            'url' => $this->getPostUrl($post),
            'blog_post_slug' => $post->getAttribute('blog_post_slug'),
        ];
    }

    /**
     * @param $slug
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function destroy($slug, BlogProvider $blogRepo, MediaProvider $mediaRepo): Response
    {
        try {
            $mediaUuids = $mediaRepo->image()
                ->getImagesFromSlug(
                    $slug,
                    Entity::BLOG_POSTS,
                    ['media_uuid']
                )->pluck('media_uuid')->all();
            $deleteResult = \DB::transaction(function () use ($slug, $blogRepo, $mediaRepo, $mediaUuids) {
                $mediaRepo->image()->delete($mediaUuids, Entity::BLOG_POSTS);
                return $blogRepo->deleteBySlug($slug);
            });
        } catch (\Exception $e) {
            return response(trans('error.http.500.general_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response($deleteResult, Response::HTTP_OK);
    }

    /**
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function batchDestroy(BlogProvider $blogRepo, MediaProvider $mediaRepo, Request $request): Response
    {
        $input = $request->only('posts');
        if (isset($input['posts'])) {
            $postSlugs = $input['posts'];
            $mediaUuids = [];
            foreach ($postSlugs as $slug) {
                $uuids = $mediaRepo->image()->getImagesFromSlug($slug, Entity::BLOG_POSTS, ['media_uuid'])
                    ->pluck('media_uuid')->all();
                if (!empty($uuids) && !is_null($uuids)) {
                    $mediaUuids = array_merge($mediaUuids, $uuids);
                }
            }
            \DB::transaction(function () use ($postSlugs, $mediaUuids, $blogRepo, $mediaRepo) {
                $mediaRepo->image()->delete($mediaUuids, Entity::BLOG_POSTS);
                $blogRepo->deleteBySlug($postSlugs);
            });
            return response(null, Response::HTTP_NO_CONTENT);
        }
        return response(trans('error.http.500.general_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $slug
     * @param string $uuid
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return array
     */
    public function setFeaturedImage($slug, $uuid, MediaProvider $mediaRepo): array
    {
        $mediaRepo->image()->setAsUsed($uuid);
        $media = $mediaRepo->image()->getOne($uuid, ['media_extension']);
        if (!is_null($media)) {
            $mediaRepo->image()->cropImageToFormat(
                $uuid,
                Entity::BLOG_POSTS,
                \Naraki\Media\Models\Media::IMAGE,
                $media->getAttribute('media_extension'),
                MediaImgFormat::FEATURED
            );
        }
        return $mediaRepo->image()->getImagesFromSlug($slug, Entity::BLOG_POSTS)->toArray();
    }

    /**
     * @param string $slug
     * @param string $uuid
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return array
     * @throws \Exception
     */
    public function deleteImage($slug, $uuid, MediaProvider $mediaRepo): array
    {
        $mediaRepo->image()->delete(
            $uuid,
            Entity::BLOG_POSTS);
        return $mediaRepo->image()->getImagesFromSlug($slug, Entity::BLOG_POSTS)->toArray();
    }

    /**
     * @param \Naraki\Blog\Models\BlogPost $post
     * @return string
     */
    private function getPostUrl($post): string
    {
        $params = [
            'slug' => $post->getAttribute('blog_post_slug'),
        ];
        if ($post->getAttribute('blog_status_id') != BlogStatus::BLOG_STATUS_PUBLISHED) {
            $params['preview'] = true;
        }
        return route_i18n('blog', $params);
    }

}
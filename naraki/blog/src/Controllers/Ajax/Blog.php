<?php namespace Naraki\Blog\Controllers\Ajax;

use App\Contracts\Models\User as UserProvider;
use App\Http\Controllers\Admin\Controller;
use App\Models\Entity;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Naraki\Blog\Contracts\Blog as BlogProvider;
use Naraki\Blog\Filters\Blog as BlogFilter;
use Naraki\Blog\Job\DeleteElasticsearch;
use Naraki\Blog\Job\UpdateElasticsearch;
use Naraki\Blog\Models\BlogStatus;
use Naraki\Blog\Requests\CreateBlogPost;
use Naraki\Blog\Requests\UpdateBlogPost;
use Naraki\Media\Facades\Media as MediaProvider;
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
     * @return array
     */
    public function edit($slug, BlogProvider $blogRepo): array
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
            'thumbnails' => MediaProvider::image()->getImages(
                $record->getAttribute('entity_type_id'))
        ];
    }

    /**
     * @param \Naraki\Blog\Requests\CreateBlogPost $request
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @param \App\Contracts\Models\User|\App\Support\Providers\User $userRepo
     * @return \Illuminate\Http\Response
     */
    public function create(CreateBlogPost $request, BlogProvider $blogRepo, UserProvider $userRepo)
    {
        try {
            $person = $userRepo->person()->buildOneBySlug(
                $request->getPersonSlug(),
                [$userRepo->person()->getKeyName()]
            )->first();
            $post = $blogRepo->createOne(
                $request->all(),
                $person
            );
            $request->setPersonSlug($person->getKey());
            $blogRepo->category()->attachToPost($request->getCategories(), $post);
            $blogRepo->tag()->attachToPost($request->getTags(), $post);
        } catch (\Exception $e) {
            return response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->dispatch(new UpdateElasticsearch(
            UpdateElasticsearch::WRITE_MODE_CREATE,
            $post,
            (object)$request->all(),
            (object)['added' => $request->getTags()],
            is_array($request->getCategories()
            )
        ));
        return response(
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
                $request->setPersonSlug($query->getKey());
            }
        }
        $post = $blogRepo->updateOne($slug, $request->all());
        $blogRepo->category()->updatePost($request->getCategories(), $post);
        $updatedTags = $blogRepo->tag()->updatePost($request->getTags(), $post);
        $this->dispatch(new UpdateElasticsearch(
            UpdateElasticsearch::WRITE_MODE_UPDATE,
            $post,
            (object)$request->all(),
            (object)$updatedTags,
            is_array($request->getCategories()
            )
        ));
        return [
            'url' => $this->getPostUrl($post),
            'blog_post_slug' => $post->getAttribute('blog_post_slug'),
        ];
    }

    /**
     * @param $slug
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function destroy($slug, BlogProvider $blogRepo): Response
    {
        try {
            $mediaUuids = MediaProvider::image()
                ->getImagesFromSlug(
                    $slug,
                    Entity::BLOG_POSTS,
                    ['media_uuid']
                )->pluck('media_uuid')->all();
            $deleteResult = \DB::transaction(function () use ($slug, $blogRepo, $mediaUuids) {
                MediaProvider::image()->delete($mediaUuids, Entity::BLOG_POSTS);
                return $blogRepo->deleteBySlug($slug);
            });
        } catch (\Exception $e) {
            return response(trans('error.http.500.general_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $this->dispatch(new DeleteElasticsearch($deleteResult));

        return response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @param \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog $blogRepo
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Throwable
     */
    public function batchDestroy(BlogProvider $blogRepo, Request $request): Response
    {
        $input = $request->only('posts');
        if (isset($input['posts'])) {
            $postSlugs = $input['posts'];
            $mediaUuids = [];
            foreach ($postSlugs as $slug) {
                $uuids = MediaProvider::image()->getImagesFromSlug($slug, Entity::BLOG_POSTS, ['media_uuid'])
                    ->pluck('media_uuid')->all();
                if (!empty($uuids) && !is_null($uuids)) {
                    $mediaUuids = array_merge($mediaUuids, $uuids);
                }
            }
            $deleteResult = \DB::transaction(function () use ($postSlugs, $mediaUuids, $blogRepo) {
                MediaProvider::image()->delete($mediaUuids, Entity::BLOG_POSTS);
                return $blogRepo->deleteBySlug($postSlugs);
            });
            $this->dispatch(new DeleteElasticsearch(
                $deleteResult
            ));
            return response(null, Response::HTTP_NO_CONTENT);
        }
        return response(trans('error.http.500.general_error'), Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param string $slug
     * @param string $uuid
     * @return array
     */
    public function setFeaturedImage($slug, $uuid): array
    {
        MediaProvider::image()->setAsUsed($uuid);
        $media = MediaProvider::image()->getOne($uuid, ['media_extension']);
        if (!is_null($media)) {
            MediaProvider::image()->cropImageToFormat(
                $uuid,
                Entity::BLOG_POSTS,
                \Naraki\Media\Models\Media::IMAGE,
                $media->getAttribute('media_extension'),
                MediaImgFormat::FEATURED
            );
        }
        return MediaProvider::image()->getImagesFromSlug($slug, Entity::BLOG_POSTS)->toArray();
    }

    /**
     * @param string $slug
     * @param string $uuid
     * @return array
     * @throws \Exception
     */
    public function deleteImage($slug, $uuid): array
    {
        MediaProvider::image()->delete(
            $uuid,
            Entity::BLOG_POSTS);
        return MediaProvider::image()->getImagesFromSlug($slug, Entity::BLOG_POSTS)->toArray();
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
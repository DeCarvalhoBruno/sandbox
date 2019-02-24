<?php namespace App\Http\Controllers\Frontend;

use App\Contracts\Models\Blog as BlogProvider;
use App\Contracts\Models\Media as MediaProvider;
use App\Jobs\ProcessPageView;
use App\Support\Frontend\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Blog extends Controller
{

    /**
     * @var \App\Contracts\Models\Blog|\App\Support\Providers\Blog
     */
    private $blogRepo;

    /**
     *
     * @param \App\Contracts\Models\Blog|\App\Support\Providers\Blog $blogRepo
     */
    public function __construct(BlogProvider $blogRepo)
    {
        $this->blogRepo = $blogRepo;
    }

    /**
     * @param $slug
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPost($slug, MediaProvider $mediaRepo)
    {
        $post = $this->blogRepo->buildOneBySlug($slug, [
            'blog_post_title as title',
            'blog_post_content as content',
            'published_at as date',
            'entity_types.entity_type_id',
            'person_slug as author',
            'full_name as person',
            'unq as page_views'
        ])->pageViews()->first();
        if (is_null($post)) {
            throw new NotFoundHttpException('Blog Post not found');
        }
        $dbImages = $mediaRepo->image()->getImages(
            $post->getAttribute('entity_type_id'), [
                'media_in_use as featured',
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type'
            ]
        );
        $categories = $this->blogRepo->buildOneBySlug(
            $slug,
            ['blog_category_slug as cat']
        )->category()->first();
        $tags = $this->blogRepo->buildOneBySlug(
            $slug, ['blog_tag_slug as tag', 'blog_tag_name as name']
        )->tag()->get();
        $sources = $this->blogRepo->source()->buildByBlogSlug($slug,['blog_source_content as source','blog_source_record_type_name as type','blog_source_description as description'])->get();
        $otherPosts = $this->blogRepo->buildOneBasic([
            'blog_post_title as title',
            'blog_post_slug as slug',
            'published_at as date',
            'entity_types.entity_type_id as type',
            'person_slug as author',
            'full_name as person',
            'unq as page_views'
        ])->pageViews()->person()->category($categories->getAttribute('cat'))->language()
            ->where('blog_post_slug', '!=', $slug)
            ->orderBy('published_at', 'desc')->limit(4)->get();
        $otherPostMedia = $this->getImages($otherPosts, $mediaRepo);
        $media = null;
        foreach ($dbImages as $image) {
            if ($image->featured == 1) {
                $media = $image;
            }
        }

        $breadcrumbs = Breadcrumbs::render([
            [
                'label' => trans(sprintf('pages.blog.category.%s', $categories->getAttribute('cat'))),
                'url' => route_i18n('blog.category', $categories->getAttribute('cat'))
            ]
        ]);
        $title = page_title($post->getAttribute('title'));
        $this->dispatch(new ProcessPageView($post));

        return view('frontend.site.blog.post', compact(
                'post', 'breadcrumbs', 'title', 'media', 'categories', 'tags', 'otherPosts', 'otherPostMedia','sources')
        );
    }

    /**
     * @param $slug
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category($slug, MediaProvider $mediaRepo)
    {
        $posts = $this->blogRepo->buildOneBasic([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'person_slug as author',
        ])->person()->category($slug)->language()
            ->orderBy('published_at', 'desc')->limit(5)->get();
        if (is_null($posts)) {
            throw new NotFoundHttpException('Blog Category not found');
        }

        $media = $this->getImages($posts, $mediaRepo);

        $featured = $posts->shift();
        $mvps = $this->getMostViewedPosts($slug);
        $mvpImages = $this->getImages(clone($mvps), $mediaRepo);

        $breadcrumbs = Breadcrumbs::render([
            [
                'label' => trans(sprintf('pages.blog.category.%s', $slug)),
            ]
        ]);

        return view(
            'frontend.site.blog.category',
            compact('breadcrumbs', 'posts', 'media', 'featured', 'mvps', 'mvpImages')
        );
    }

    public function tag($slug, MediaProvider $mediaRepo)
    {
        $posts = $this->blogRepo->buildOneBasic([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'entity_types.entity_type_id as type',
            'person_slug as author',
            'blog_tag_name as tag'
        ])->person()->tag($slug)->language()
            ->orderBy('published_at', 'desc')->limit(8)->get();
        if (is_null($posts)) {
            throw new NotFoundHttpException('Blog Tag not found');
        }
        $tag = (object)$posts->first()->only(['tag']);
        $media = $this->getImages($posts, $mediaRepo);
        return view(
            'frontend.site.blog.tag', compact('posts', 'media', 'tag'));
    }

    /**
     * @param $slug
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function author($slug, MediaProvider $mediaRepo)
    {
        $posts = $this->blogRepo->buildOneBasic([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'full_name as author'
        ])->person($slug)->language()->category()
            ->orderBy('published_at', 'desc')->limit(8)->get();
        $author = (object)$posts->first()->only(['author']);
        if (is_null($posts)) {
            throw new NotFoundHttpException('Author not found');
        }
        $media = $this->getImages($posts, $mediaRepo);
//        dd($posts,$media);
        return view(
            'frontend.site.blog.author', compact('posts', 'media', 'author')
        );
    }

    private function getImages($collection, $mediaRepo)
    {
        $dbImages = $mediaRepo->image()->getImages(
            $collection->pluck('type')->all(), [
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type'
            ]
        );
        $media = [];
        foreach ($dbImages as $image) {
            $media[$image->type] = $image;
        }
        return $media;
    }

    private function getMostViewedPosts($slug)
    {
        return $this->blogRepo->buildOneBasic([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'person_slug as author',
            'unq as page_views'
        ])->person()->category($slug)->language()->pageViews()
            ->orderBy('page_views', 'desc')->limit(10)->get();
    }

}
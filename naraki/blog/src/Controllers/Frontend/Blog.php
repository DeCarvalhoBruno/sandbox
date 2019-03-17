<?php namespace Naraki\Blog\Controllers\Frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Language;
use Naraki\Blog\Facades\Blog as BlogFacade;
use Naraki\Media\Contracts\Media as MediaProvider;
use App\Jobs\ProcessPageView;
use App\Support\Frontend\Breadcrumbs;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Blog extends Controller
{

    /**
     * @var \Naraki\Blog\Contracts\Blog|\Naraki\Blog\Providers\Blog
     */
    private $blogRepo;

    public function __construct()
    {
        $this->blogRepo = BlogFacade::blog();
    }

    /**
     * @param $slug
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPost($slug, MediaProvider $mediaRepo)
    {
        $post = $this->blogRepo->buildOneBySlug($slug, [
            'blog_post_title as title',
            'blog_post_content as content',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'entity_types.entity_type_id',
            'person_slug as author',
            'full_name as person',
            'unq as page_views',
            'language_id as language',
            'published_at as date_modified',
            'published_at as date_published'
        ])->scopes(['pageViews'])->first();
        if (is_null($post)) {
            throw new NotFoundHttpException('Blog Post not found');
        }
        $dbImages = $mediaRepo->image()->getImages(
            $post->getAttribute('entity_type_id'), [
                'media_in_use as featured',
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type',
                'entity_id'
            ]
        );
        $categories = $this->blogRepo->buildOneBySlug($slug, ['blog_category_slug as cat'])
            ->scopes(['category'])
            ->orderBy('blog_category_id', 'asc')
            ->get();
        $firstCategory = $categories->first();
        $tags = $this->blogRepo->buildOneBySlug($slug, ['blog_tag_slug as tag', 'blog_tag_name as name'])
            ->scopes(['tag'])
            ->get();

        $sources = $this->blogRepo->source()->buildByBlogSlug($slug)->get();
        $otherPosts = $this->blogRepo->buildSimple([
            'blog_post_title as title',
            'blog_post_slug as slug',
            'published_at as date',
            'entity_types.entity_type_id as type',
            'person_slug as author',
            'full_name as person',
            'unq as page_views'
        ], ['entityType', 'pageViews', 'person', 'language', 'category' => $firstCategory->getAttribute('cat')])
            ->where('blog_post_slug', '!=', $slug)
            ->orderBy('published_at', 'desc')->limit(4)->get();

        $otherPostMedia = $this->getImages($otherPosts, $mediaRepo);
        $media = null;
        foreach ($dbImages as $image) {
            if ($image->featured == 1) {
                $media = $image;
            }
        }

        $breadcrumbs = [];
        foreach ($categories as $cat) {
            $breadcrumbs[] = [
                'label' => trans(sprintf('pages.blog.category.%s', $cat->getAttribute('cat'))),
                'url' => route_i18n('blog.category', $cat->getAttribute('cat'))
            ];
        }
        $this->dispatch(new ProcessPageView($post));

        return view('blog::post', compact(
                'post', 'breadcrumbs', 'media', 'categories', 'tags', 'otherPosts', 'otherPostMedia', 'sources')
        );
    }

    /**
     * @param $slug
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category($slug, MediaProvider $mediaRepo)
    {
        $posts = $this->blogRepo->buildSimple([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'person_slug as author',
        ], ['entityType', 'person', 'language', 'category' => $slug])
            ->orderBy('published_at', 'desc')
            ->where('language_id', Language::getAppLanguageId())
            ->limit(5)
            ->get();
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
        $title = trans('titles.routes.blog.category', ['name' => $featured->getAttribute('cat')]);

        return view(
            'blog::category',
            compact('breadcrumbs', 'posts', 'media', 'featured', 'mvps', 'mvpImages', 'title')
        );
    }

    public function tag($slug, MediaProvider $mediaRepo)
    {
        $posts = $this->blogRepo->buildSimple([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'entity_types.entity_type_id as type',
            'person_slug as author',
            'blog_tag_name as tag'
        ], ['entityType', 'person', 'tag' => $slug])
            ->orderBy('published_at', 'desc')
            ->where('language_id', Language::getAppLanguageId())
            ->limit(8)
            ->get();
        if (is_null($posts)) {
            throw new NotFoundHttpException('Blog Tag not found');
        }
        $tag = (object)$posts->first()->only(['tag']);
        $media = $this->getImages($posts, $mediaRepo);
        $title = trans('titles.routes.blog.tag', ['name' => $tag->tag]);
        return view(
            'blog::tag', compact('posts', 'media', 'tag', 'title'));
    }

    /**
     * @param $slug
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function author($slug, MediaProvider $mediaRepo)
    {
        $posts = $this->blogRepo->buildSimple([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'full_name as author'
        ], ['entityType', 'person' => $slug, 'language', 'category'])
            ->where('parent_id', null)
            ->orderBy('published_at', 'desc')
            ->limit(8)
            ->get();

        $author = (object)$posts->first()->only(['author']);
        if (is_null($posts)) {
            throw new NotFoundHttpException('Author not found');
        }
        $media = $this->getImages($posts, $mediaRepo);
        $title = trans('titles.routes.blog.author', ['name' => $author->author]);
        return view(
            'blog::author', compact('posts', 'media', 'author', 'title')
        );
    }

    private function getImages($collection, $mediaRepo)
    {
        $dbImages = $mediaRepo->image()->getImages(
            $collection->pluck('type')->all(), [
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type',
                'entity_types.entity_id as entity_id'
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
        return $this->blogRepo->buildSimple([
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'person_slug as author',
            'unq as page_views'
        ], ['entityType', 'person', 'category' => $slug, 'language', 'pageViews'])
            ->orderBy('page_views', 'desc')->limit(10)->get();
    }

}
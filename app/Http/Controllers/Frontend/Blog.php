<?php namespace App\Http\Controllers\Frontend;

use App\Contracts\Models\Blog as BlogProvider;
use App\Contracts\Models\Media as MediaProvider;
use App\Jobs\ProcessPageView;
use App\Models\Entity;
use App\Models\Media\Media;
use App\Models\Media\MediaImgFormat;
use App\Support\Frontend\Breadcrumbs;
use Carbon\Carbon;

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
            'blog_category_slug as cat',
            'entity_types.entity_type_id',
            'person_slug as person',
            'unq as page_views'
        ])->pageViews()->first();
        $dbImages = $mediaRepo->image()->getImages(
            $post->getAttribute('entity_type_id'), [
                'media_in_use as featured',
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type'
            ]
        );

        $media = null;
        foreach ($dbImages as $image) {
            if ($image->featured == 1) {
                $media = $image;
            }
        }

        $title = page_title($post->getAttribute('title'));
        $breadcrumbs = Breadcrumbs::render([
            [
                'label' => trans(sprintf('pages.blog.category.%s', $post->getAttribute('cat'))),
                'url' => route_i18n('blog.category', $post->getAttribute('cat'))
            ]
        ]);

        $this->dispatch(new ProcessPageView($post));

        return view('frontend.site.blog.post', compact(
                'post', 'breadcrumbs', 'title', 'media')
        );
    }

    /**
     * @param $slug
     * @param \App\Contracts\Models\Media|\App\Support\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function category($slug, MediaProvider $mediaRepo)
    {
        $posts = $this->blogRepo->buildOneByCat($slug, [
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'person_slug as author',
        ])->language()->orderBy('published_at', 'desc')->limit(5)->get();

        $media = $this->getImages($posts, $mediaRepo);

        $featured = $posts->shift();
        $mvps = $this->getMostViewedPosts($slug);
        $mvpImages = $this->getImages(clone($mvps), $mediaRepo);

        $breadcrumbs = Breadcrumbs::render([
            [
                'label' => trans(sprintf('pages.blog.category.%s', $slug)),
            ]
        ]);
//        dd($mvps);
        return view(
            'frontend.site.blog.category',
            compact('breadcrumbs', 'posts', 'media', 'featured', 'mvps', 'mvpImages')
        );
    }

    public function tag($slug)
    {

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
        return $this->blogRepo->buildOneByCat($slug, [
            'blog_post_title as title',
            'blog_post_excerpt as excerpt',
            'blog_post_slug as slug',
            'published_at as date',
            'blog_category_slug as cat',
            'entity_types.entity_type_id as type',
            'person_slug as author',
            'unq as page_views'
        ])->language()->pageViews()->orderBy('page_views', 'desc')->limit(10)->get();
    }

}
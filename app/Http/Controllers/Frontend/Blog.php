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
        $post->setAttribute('date', new Carbon($post->getAttribute('date')));
        $dbImages = $mediaRepo->image()->getImages(
            $post->getAttribute('entity_type_id'), [
                'media_in_use as featured',
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type'
            ]
        );

        foreach ($dbImages as $image) {
            if ($image->featured == 1) {
                $image->setAttribute(
                    'img',
                    media_entity_path(
                        Entity::BLOG_POSTS,
                        Media::IMAGE,
                        sprintf(
                            '%s_%s.%s',
                            $image->getAttribute('uuid'),
                            MediaImgFormat::getFormatAcronyms(MediaImgFormat::FEATURED),
                            $image->getAttribute('ext')
                        )
                    )
                );
                $media['featured'] = $image;

            } else {
                $media['media'][] = $image;
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

    public function category($slug)
    {

    }

    public function tag($slug)
    {

    }

}
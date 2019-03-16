<?php namespace App\Http\Controllers\Frontend;

use Naraki\Media\Contracts\Media as MediaProvider;
use App\Models\Language;
use Naraki\Blog\Facades\Blog;

class Home extends Controller
{

    /**
     * @param \Naraki\Media\Contracts\Media|\Naraki\Media\Providers\Media $mediaRepo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(MediaProvider $mediaRepo)
    {
        $dbResult = Blog::buildForDisplay()
            ->orderBy('page_views', 'desc')
            ->where('language_id', Language::getAppLanguageId())
            ->where('blog_categories.parent_id',null)
            ->limit(115)
            ->get();
        $dbImages = $mediaRepo->image()->getImages(
            $dbResult->pluck('type')->all(), [
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type',
                'entity_id'
            ]
        );
        $media = [];
        foreach ($dbImages as $image) {
            $media[$image->type] = $image;
        }
        $posts = [
            'featured' => [],
            'most_viewed_cat' => [],
            'most_viewed' => []
        ];

        foreach ($dbResult as $post) {
            if ($post->featured == 1) {
                if (isset($media[$post->type])) {
                    $posts['featured'][] = $post;
                }
            } else {
                if (isset($media[$post->type])) {
                    if (isset($posts['most_viewed_cat'][$post->cat])) {
                        if (count($posts['most_viewed_cat'][$post->cat]) < 5) {
                            $posts['most_viewed_cat'][$post->cat][] = $post;
                        } else {
                            if (count($posts['most_viewed']) < 18) {
                                $posts['most_viewed'][] = $post;
                            }
                        }
                    } else {
                        $posts['most_viewed_cat'][$post->cat][] = $post;
                    }
                }
            }
        }
        unset($dbResult);
        return view('frontend.site.home', compact('posts','media'));
    }

}
<?php namespace App\Http\Controllers\Frontend;

use App\Models\Entity;
use App\Models\Media\Media;
use App\Models\Media\MediaImgFormat;
use App\Contracts\Models\Blog as BlogProvider;
use App\Contracts\Models\Media as MediaProvider;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Home extends Controller
{

    public function index(BlogProvider $blogRepo, MediaProvider $mediaRepo)
    {
        $dbResult = $blogRepo->buildForDisplay()->get();
        $result2 = clone($dbResult);

        $dbImages = $mediaRepo->image()->getImages(
            $result2->pluck('type')->all(), [
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type'
            ]
        );
        $images = [];
        foreach ($dbImages as $image) {
            $images[$image->type] = $image;
        }

        $posts = [
            'featured' => [],
            'most_viewed_cat' => [],
            'most_viewed' => []
        ];
        foreach ($dbResult as $post) {
            $post->setAttribute('date', new Carbon($post->getAttribute('time')));
            $post->setAttribute('title', Str::limit($post->getAttribute('title'),100));
            if (isset($images[$post->type])) {
                $post->setAttribute(
                    'img',
                    media_entity_path(
                        Entity::BLOG_POSTS,
                        Media::IMAGE,
                        sprintf(
                            '%s_%s.%s',
                            $images[$post->type]->getAttribute('uuid'),
                            MediaImgFormat::getFormatAcronyms(MediaImgFormat::FEATURED),
                            $images[$post->type]->getAttribute('ext'))
                    )
                );
            }
            if ($post->featured == 1) {
                if (isset($images[$post->type])) {
                    $posts['featured'][] = $post->toArray();
                }
            } else {
                if (isset($images[$post->type])) {
                    if (isset($posts['most_viewed_cat'][$post->cat])) {
                        if (count($posts['most_viewed_cat'][$post->cat]) < 5) {
                            $posts['most_viewed_cat'][$post->cat][] = $post->toArray();
                        } else {
                            if (count($posts['most_viewed']) < 18) {
                                $posts['most_viewed'][] = $post->toArray();
                            }
                        }
                    } else {
                        $posts['most_viewed_cat'][$post->cat][] = $post->toArray();
                    }
                }
            }
        }
        unset($dbResult);
//        dd($posts);
        return view('frontend.site.home', compact('posts'));
    }

}
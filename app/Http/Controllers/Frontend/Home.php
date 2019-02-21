<?php namespace App\Http\Controllers\Frontend;

use App\Models\Entity;
use App\Models\Media\Media;
use App\Models\Media\MediaImgFormat;
use App\Models\Media\MediaImgFormatType;
use App\Support\Providers\Blog as BlogProvider;
use App\Support\Providers\Media as MediaProvider;
use Carbon\Carbon;

class Home extends Controller
{

    public function index(BlogProvider $blogRepo, MediaProvider $mediaRepo)
    {
        //$mediaRepo->image()->getImages();

        //title date cat author featured
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

//        dd((clone($dbResult)->pluck('entity_type_id')->all()));
        $posts = ['featured' => [], 'most_viewed' => []];
//        $entityIds = [];
        foreach ($dbResult as $post) {
            $post->setAttribute('date',new Carbon($post->getAttribute('time')));
            if (isset($images[$post->type])) {
                $post->setAttribute(
                    'img',
                    media_entity_path(
                        Entity::BLOG_POSTS,
                        Media::IMAGE,
                        sprintf(
                            '%s_%s.%s',
                            $images[$post->type]->uuid,
                            MediaImgFormat::getFormatAcronyms(MediaImgFormat::FEATURED),
                            $images[$post->type]->ext)
                    )
                );
            }
            if ($post->featured == 1) {
                if (isset($images[$post->type])) {
                    $posts['featured'][] = $post->toArray();
//                    $entityIds[] = $post->type;
                }
            } else {
                if (isset($images[$post->type])) {
                    if (isset($posts['most_viewed'][$post->cat])) {
                        if (count($posts['most_viewed'][$post->cat]) < 5) {
                            $posts['most_viewed'][$post->cat][] = $post->setAttribute(
                                'img',
                                media_entity_path(
                                    Entity::BLOG_POSTS,
                                    Media::IMAGE,
                                    sprintf(
                                        '%s_%s.%s',
                                        $images[$post->type]->uuid,
                                        MediaImgFormat::getFormatAcronyms(MediaImgFormat::THUMBNAIL),
                                        $images[$post->type]->ext)
                                )
                            )->toArray();
//                            $entityIds[] = $post->type;
                        }
                    } else {
                        $posts['most_viewed'][$post->cat][] =$post->toArray();
//                        $entityIds[] = $post->type;
                    }
                }
            }
        }
        unset($dbResult);

/*
 "featured" => array:6 [▼
    0 => array:7 [▼
      "title" => "Grammys Producer Ken Ehrlich: Ariana Grande Attack ‘Was a Surprise’"
      "date" => "2019-02-09 21:03:00"
      "cat" => "entertainment"
      "author" => "Amy X. Wang"
      "featured" => 1
      "type" => 4931
      "img" => "/media/blog_posts/image/0777b544936d470db6696d3f4b5ef692_ft.jpg"
    ]

  "most_viewed" => array:6 [▼
    "entertainment" => array:5 [▼
      0 => array:7 [▼
        "title" => "Berlin Opening Gala Pays Tribute to Outgoing Director Dieter Kosslick"
        "date" => "2019-02-08 02:55:13"
        "cat" => "entertainment"
        "author" => "Scott Roxborough"
        "featured" => 0
        "type" => 3740
        "img" => "/media/blog_posts/image/f01d4f44367441fbbc0d897eb042586e_ft.jpg"
      ]
 */
//        dd($posts);

        return view('frontend.site.home',compact('posts'));
    }

}
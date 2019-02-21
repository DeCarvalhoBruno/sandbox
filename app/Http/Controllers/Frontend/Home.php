<?php namespace App\Http\Controllers\Frontend;

use App\Support\Providers\Blog as BlogProvider;
use App\Support\Providers\Media as MediaProvider;

class Home extends Controller
{

    public function index(BlogProvider $blogRepo, MediaProvider $mediaRepo)
    {
        //$mediaRepo->image()->getImages();

        //title date cat author featured
        $dbResult = $blogRepo->buildForDisplay()->get();
        $posts = ['featured' => [], 'most_viewed' => []];
        $entityIds = [];
        foreach ($dbResult as $post) {
            if ($post->featured == 1) {
                $posts['featured'][] = $post->toArray();
                $entityIds[] = $post->type;
            } else {
                if (isset($posts['most_viewed'][$post->cat])) {
                    if (count($posts['most_viewed'][$post->cat]) < 5) {
                        $posts['most_viewed'][$post->cat][] = $post->toArray();
                        $entityIds[] = $post->type;
                    }
                } else {
                    $posts['most_viewed'][$post->cat][] = $post->toArray();
                    $entityIds[] = $post->type;
                }
            }
        }

        $images = $mediaRepo->image()->getImages($entityIds);

        return view('frontend.site.home');
    }

}
<?php namespace App\Http\Controllers;

use Naraki\Blog\Facades\Blog;
use Naraki\Blog\Support\Collections\Blog as BlogCollection;
use Naraki\Core\Controllers\Frontend\Home as HomeController;
use Naraki\Media\Facades\Media as MediaProvider;

class Home extends HomeController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $dbResult = Blog::mostViewedByCategoryTotal();
        $posts = [
            'featured' => [],
            'mvpcat' => [],
            'most_viewed' => []
        ];
        $types = [];
        foreach ($dbResult as $result) {
            $types[] = $result->type;
        }
        $dbImages = MediaProvider::image()->getImages(
            $types, [
                'media_uuid as uuid',
                'media_extension as ext',
                'entity_types.entity_type_id as type',
                'entity_id'
            ]
        );
        $mediaTmp = $media = [];
        foreach ($dbImages as $image) {
            $mediaTmp[$image->type] = $image;
        }

        foreach ($dbResult as $post) {
            if ($post->featured == 1) {
                if (isset($mediaTmp[$post->type])) {
                    $posts['featured'][] = new BlogCollection((array)$post);
                    $media[$post->type] = $mediaTmp[$post->type];
                }
            } else {
                if (isset($mediaTmp[$post->type])) {
                    if (isset($posts['mvpcat'][$post->cat])) {
                        if (count($posts['mvpcat'][$post->cat]) < 5) {
                            $posts['mvpcat'][$post->cat][] = new BlogCollection((array)$post);
                            $media[$post->type] = $mediaTmp[$post->type];
                        } else {
                            if (count($posts['most_viewed']) < 18) {
                                $posts['most_viewed'][] = new BlogCollection((array)$post);
                                $media[$post->type] = $mediaTmp[$post->type];
                            }
                        }
                    } else {
                        $posts['mvpcat'][$post->cat][] = new BlogCollection((array)$post);
                        $media[$post->type] = $mediaTmp[$post->type];
                    }
                }
            }
        }
        unset($dbResult);
        return view('core::frontend.site.home', compact('posts', 'media'));
    }

}
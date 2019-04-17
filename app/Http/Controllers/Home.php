<?php namespace App\Http\Controllers;

use Naraki\Blog\Facades\Blog;
use Naraki\Core\Controllers\Frontend\Home as HomeController;
use Naraki\Core\Models\Language;
use Naraki\Media\Facades\Media as MediaProvider;

class Home extends HomeController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $dbResult = Blog::buildForDisplay()
            ->orderBy('page_views', 'desc')
            ->where('language_id', Language::getAppLanguageId())
            ->where('blog_categories.parent_id', null)
            ->limit(150)
            ->get();
        $dbImages = MediaProvider::image()->getImages(
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
            if (empty($post->cat)) {
                continue;
            }
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
        return view('core::frontend.site.home', compact('posts', 'media'));
    }

}
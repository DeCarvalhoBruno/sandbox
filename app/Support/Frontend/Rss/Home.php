<?php namespace App\Support\Frontend\Rss;

use App\Models\Language;
use Naraki\Blog\Facades\Blog;
use Naraki\Media\Facades\Media;
use Naraki\Rss\Contracts\RssFeedable;
use Naraki\Rss\Feeds\Feed;

class Home extends Feed implements RssFeedable
{

    public function __toString(): string
    {
        list($post, $media) = $this->getData();

        return '';
    }

    private function getData()
    {
        $dbResult = Blog::buildForDisplay()
            ->orderBy('page_views', 'desc')
//            ->orderBy('published_at', 'desc')
            ->where('language_id', Language::getAppLanguageId())
            ->where('blog_categories.parent_id', null)
            ->limit(15)
            ->get();
        $dbImages = Media::image()->getImages(
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

        return [$dbResult, $media];
    }

}
<?php namespace App\Support\Frontend\Social;

use App\Models\Language;
use Carbon\Carbon;

class Blog
{
    public function getFacebookTagList(\stdClass $data): string
    {
        $tagList = [
            'og:title' => $data->post->getAttribute('title'),
            'og:url' => route_i18n('blog', ['slug' => $data->post->getAttribute('slug')]),
            'og:image' => asset($data->media->present('asset')),
            'og:description' => $data->post->getAttribute('excerpt'),
            'og:site_name' => config('app.name'),
            'og:author' => $data->post->getAttribute('person'),
            'og:type' => 'article',
            'og:locale' => Language::getLanguageName(intval($data->post->getAttribute('language'))),
            'og:article:published_time' => (new Carbon
            ($data->post->getAttribute('date_published')))->format('Y-m-d\TH:i:s'
            ),
        ];
        if (!is_null($data->settings['facebook_app_id']) && !empty($data->settings['facebook_app_id'])) {
            $tagList['fb:app_id'] = $data->settings['facebook_app_id'];
        }
        $tags = '';
        foreach ($tagList as $k => $v) {
            $tags .= sprintf('<meta property="%s" content="%s">', $k, $v);
        }
        return $tags;
    }

    public function getTwitterTagList(\stdClass $data): string
    {
        $tagList = [
            'twitter:title' => $data->post->getAttribute('title'),
            'twitter:description' => $data->post->getAttribute('excerpt'),
            'twitter:image:src' => asset($data->media->present('asset')),
            'twitter:site' => $data->settings['twitter_publisher'],
            'twitter:card' => 'summary_large_image'
        ];
        $tags = '';
        foreach ($tagList as $k => $v) {
            $tags .= sprintf('<meta name="%s" content="%s">', $k, $v);
        }
        return $tags;
    }
}
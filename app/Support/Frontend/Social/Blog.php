<?php namespace App\Support\Frontend\Social;

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
        ];
        $tags = '';
        foreach ($tagList as $k => $v) {
            $tags .= sprintf('<meta name="%s" content="%s">', $k, $v);
        }
        return $tags;
    }
}
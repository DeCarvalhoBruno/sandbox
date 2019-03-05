<?php namespace App\Support\Frontend\Jsonld\Models;

use App\Support\Frontend\Jsonld\JsonLd;
use App\Support\Frontend\Jsonld\Schemas\Thing\CreativeWork\Article\NewsArticle;
use App\Support\Frontend\Jsonld\Schemas\Thing\Intangible\BreadcrumbList;
use Carbon\Carbon;

class Blog extends General
{
    public function makeStructuredData($data): string
    {

        $structuredData = [];
        $class = BreadcrumbList::class;
        $jsonld = ['itemListElement' => []];
        foreach ($data->breadcrumbs as $idx => $breadCrumb) {
            $jsonld['itemListElement'][] = [
                'position' => $idx + 1,
                'name' => $breadCrumb['label'],
                'item' => $breadCrumb['url']
            ];
        }
        $structuredData[] = (object)compact('class', 'jsonld');
        $class = NewsArticle::class;

        $jsonld = [
            'mainEntityOfPage@WebPage' => [
                'url' => route_i18n('blog', ['slug' => $data->post->getAttribute('slug')])
            ],
            'headline' => $data->post->getAttribute('title'),
            'image' => [
                asset($data->media->present('asset')),
                asset($data->media->present('thumbnail')),
            ],
            'datePublished' => (new Carbon($data->post->getAttribute('date_published')))->format('Y-m-d\TH:i:s'),
            'dateModified' => (new Carbon($data->post->getAttribute('date_modified')))->format('Y-m-d\TH:i:s'),
            'author@Person' => [
                '@id' => sprintf('%s#person', route_i18n(
                        'blog.author',
                        ['slug' => $data->post->getAttribute('author')])
                )
            ],
            'description' => $data->post->getAttribute('excerpt')
        ];
        if ($this->settings['entity_type'] === 'person') {
            $jsonld['publisher'] = [
                '@id' => sprintf('%s#%s', $this->settings['person_url'], 'person')
            ];
        } else {
            $jsonld['publisher'] = [
                '@id' => sprintf('%s#%s', $this->settings['org_url'], strtolower($this->settings['org_type']))
            ];
        }
        $structuredData[] = (object)compact('class', 'jsonld');

        return JsonLd::generate($structuredData);

    }

}
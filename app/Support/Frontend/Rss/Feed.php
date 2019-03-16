<?php namespace App\Support\Frontend\Rss;

use App\Models\Language;
use Carbon\Carbon;
use Naraki\Rss\Feeds\Feed as AbstractFeed;

abstract class Feed extends AbstractFeed
{
    protected function makeFeed($url, $feedUrl)
    {
        $nowDate = Carbon::now();
        $this->buildFeed((object)[
            'title' => \Cache::get('meta_title'),
            'description' => \Cache::get('meta_description'),
            'url' => $url,
            'feedUrl' => $feedUrl,
            'locale' => Language::getAppLanguageISO639(),
            'copyrightDate' => $nowDate->year,
            'copyrightName' => config('app.name'),
            'nowDate' => strtotime($nowDate->toDateTimeString()),
        ]);
    }

}
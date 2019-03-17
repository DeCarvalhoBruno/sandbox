<?php namespace Naraki\Sitemap\Sitemaps;

use Carbon\Carbon;
use Naraki\Blog\Facades\Blog as BlogRepo;
use Naraki\Sitemap\Contracts\Sitemapable;
use Thepixeldeveloper\Sitemap\SitemapIndex;
use Thepixeldeveloper\Sitemap\Urlset;

class Pages extends Sitemap implements Sitemapable
{
    private $perPage = 1000;
    protected $priority = 0.7;
    protected $changeFrequency = 'yearly';

    public function __construct($slug)
    {
        $this->slug = $slug;
        parent::__construct();
    }

    public function __toString(): string
    {
        $path = base_path('resources/views/frontend/default.blade.php');
        if(is_file($path)){
        $stat = @stat($path);
        if($stat!==false){
            $date = Carbon::createFromTimestamp($stat['mtime']);
        }

        }
        //resources/views/frontend/default.blade.php
        $sm = new Urlset();


        return $this->output($sm);
    }

}
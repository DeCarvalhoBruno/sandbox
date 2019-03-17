<?php namespace Naraki\Sitemap\Sitemaps;

use Carbon\Carbon;
use Naraki\Blog\Facades\Blog as BlogRepo;
use Naraki\Sitemap\Contracts\Sitemapable;
use Thepixeldeveloper\Sitemap\SitemapIndex;

class Main extends Sitemap implements Sitemapable
{

    public function __construct()
    {
        parent::__construct();
    }

    public function __toString(): string
    {
        $smIndex = new SitemapIndex();
        $lastModified = Carbon::make(BlogRepo::getNth(0, 'updated_at'));

        $this->addSitemap($smIndex, route('sitemap', ['type' => 'pages']),
            Carbon::now()
        );
        $this->addSitemap($smIndex, route('sitemap', ['type' => 'blog']),
            $lastModified
        );
        $this->addSitemap($smIndex, route('sitemap', ['type' => 'authors']),
            $lastModified
        );
        $this->addSitemap($smIndex, route('sitemap', ['type' => 'tags']),
            $lastModified
        );

        return $this->output($smIndex);
    }

}
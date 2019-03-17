<?php namespace Naraki\Sitemap\Controllers;

use Illuminate\Routing\Controller;
use Naraki\Sitemap\Contracts\Sitemapable;

class Sitemap extends Controller
{
    public function __invoke($type, $slug = null)
    {
        $feedableClasses = config('sitemap.aliases');
        if (!isset($feedableClasses[$type])) {
            throw new \UnexpectedValueException(
                sprintf('%s does not have an sitemap maker', $type)
            );
        }
        $instance = new $feedableClasses[$type]($slug);
        if (!$instance instanceof Sitemapable) {
            throw new \UnexpectedValueException(
                sprintf('%s must implement interface \Naraki\Sitemap\Contracts\Sitemapable', get_class($instance))
            );
        }
        return $instance;
    }

}
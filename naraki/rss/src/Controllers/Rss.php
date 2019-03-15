<?php namespace Naraki\Rss\Controllers;

use App\Http\Controllers\Frontend\Controller;

class Rss extends Controller
{
    /**
     * @return \Illuminate\Contracts\Support\Responsable
     */
    public function __invoke($type, $slug = null)
    {
        $className = '\Naraki\Rss\Feeds\\' . ucfirst($type);
        if (!class_exists($className)) {
            throw new \UnexpectedValueException(
                sprintf('%s does not have an RSS feed maker', $type)
            );
        }
        return new $className($slug);
    }
}
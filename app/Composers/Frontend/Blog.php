<?php namespace App\Composers\Frontend;

use App\Composers\Composer;
use App\Support\Frontend\Breadcrumbs;
use App\Support\Frontend\Jsonld\Models\Blog as JsonldBlog;

class Blog extends Composer
{
    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $jsonldManager = new JsonldBlog();
        $data = $view->getData();
        $data['title'] = page_title($data['post']->getAttribute('title'));
        $hasJsonld = \Cache::get('settings_has_jsonld');

        if (!is_null($hasJsonld) && $hasJsonld === true) {
            $data['meta_jsonld'] = $jsonldManager->makeStructuredData((object)[
                'breadcrumbs' => $data['breadcrumbs'],
                'post' => $data['post'],
                'media' => $data['media']
            ]);
            $data['breadcrumbs'] = Breadcrumbs::render($data['breadcrumbs']);
        }
        $this->addVarsToView($data, $view);
    }
}
<?php namespace Naraki\Blog\Composers;

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
        }
        $data['breadcrumbs'] = Breadcrumbs::render($data['breadcrumbs']);
        $hasFacebook = \Cache::get('settings_has_facebook');
        $socialSettings = \Cache::get('settings_social');
        $socialTagManager = new \Naraki\Blog\Support\Social\Blog();
        $socialData = (object)[
            'post' => $data['post'],
            'media' => $data['media'],
            'settings' => $socialSettings
        ];
        if (!is_null($hasFacebook) && $hasFacebook === true) {
            $data['meta_facebook'] = $socialTagManager->getFacebookTagList($socialData);
        }

        $hasTwitter = \Cache::get('settings_has_twitter');
        if (!is_null($hasTwitter) && $hasTwitter === true) {
            $data['meta_twitter'] = $socialTagManager->getTwitterTagList($socialData);
        }
        $this->addVarsToView($data, $view);
    }
}
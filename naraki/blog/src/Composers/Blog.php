<?php namespace Naraki\Blog\Composers;

use App\Composers\Composer;
use App\Support\Frontend\Breadcrumbs;
use App\Support\Frontend\Jsonld\Models\Blog as JsonldBlog;
use Naraki\System\Support\Settings;

class Blog extends Composer
{
    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = $view->getData();
        $data['title'] = page_title($data['post']->getAttribute('title'));
        $generalSettings = new Settings('general_formatted');

        $hasJsonld = $generalSettings->get('has_jsonld');
        if (!is_null($hasJsonld) && $hasJsonld === true) {
            $data['meta_jsonld'] = JsonldBlog::makeStructuredData((object)[
                'breadcrumbs' => $data['breadcrumbs'],
                'post' => $data['post'],
                'media' => $data['media']
            ], $generalSettings);
        }
        $data['breadcrumbs'] = Breadcrumbs::render($data['breadcrumbs']);
        $socialSettings = new Settings('social_formatted');

        $socialTagManager = new \Naraki\Blog\Support\Social\Blog();
        $socialData = (object)[
            'post' => $data['post'],
            'media' => $data['media'],
        ];

        $hasFacebook = $socialSettings->get('has_facebook');
        if (!is_null($hasFacebook) && $hasFacebook === true) {
            $data['meta_facebook'] = $socialTagManager->getFacebookTagList($socialData,$socialSettings);
        }

        $hasTwitter = $socialSettings->get('has_twitter');
        if (!is_null($hasTwitter) && $hasTwitter === true) {
            $data['meta_twitter'] = $socialTagManager->getTwitterTagList($socialData,$socialSettings);
        }
        $this->addVarsToView($data, $view);
    }
}
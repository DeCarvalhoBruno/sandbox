<?php namespace App\Composers\Frontend;

use App\Composers\Composer;

class Blog extends Composer
{
    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = $view->getData();
        $data['title'] = page_title($data['post']->getAttribute('title'));
        $hasJsonld = \Cache::get('settings_has_jsonld');
        if (!is_null($hasJsonld) && $hasJsonld === true) {
            $data['meta_jsonld'] = null;
        }
        $this->addVarsToView($data, $view);
    }
}
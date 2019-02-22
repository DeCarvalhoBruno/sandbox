<?php namespace App\Composers;

use App\Facades\JavaScript;
use App\Support\Frontend\Breadcrumbs;

class Frontend extends Composer
{
    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $this->checkFlashMessages();
        $data = [];
        $existingData = $view->getData();
        if (!isset($existingData['title'])) {
            $data['title'] = page_title(
                trans(
                    sprintf(
                        'titles.%s',
                        (!is_null($view)) ? str_replace('.', '_', $view->getName()) : 'error'
                    )
                )
            );
        }
        $data['user'] = auth()->user();
        JavaScript::putArray([
            'locale' => app()->getLocale(),
        ]);
        JavaScript::bindJsVariablesToView();
        $this->addVarsToView($data, $view);
    }

    private function checkFlashMessages()
    {
        if (\Session::has('msg')) {
            JavaScript::putArray([
                'msg' => \Session::pull('msg'),
            ]);
        }

    }
}
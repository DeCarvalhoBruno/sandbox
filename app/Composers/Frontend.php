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
        $data = [
            'title' => sprintf(
                '%s - %s',
                trans(
                    sprintf(
                        'titles.%s',
                        str_replace('.', '_', $view->getName())
                    )
                ),
                config('app.name')
            ),
            'user' => auth()->user()
        ];

        $originalData = $view->getData();
        if (isset($originalData['breadcrumbs'])) {
            $data['breadcrumbs'] = Breadcrumbs::render($originalData['breadcrumbs']);
        }
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
<?php namespace App\Composers;

use App\Facades\JavaScript;

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
        $data['search_url'] = env('ELASTIC_SEARCH_URL');
        JavaScript::putArray([
            'locale' => app()->getLocale(),
            'gapi_client' => env('OAUTH_GOOGLE_CLIENT_ID'),
            'auth_check' => auth()->check(),
            'google_verified'=>request()->cookies->has('google_verified')
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
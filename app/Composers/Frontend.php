<?php namespace App\Composers;
use App\Facades\JavaScript;

class Frontend extends Composer
{
    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = [
            'title' => sprintf(
                '%s - %s',
                trans(
                    sprintf(
                        'titles.%s',
                        str_replace('.', '_', $view->getName())
                    )
                ),
                config('app.name'))
        ];
        JavaScript::putArray([
            'locale' => app()->getLocale(),
        ]);
        JavaScript::bindJsVariablesToView();
        $this->addVarsToView($data, $view);
    }
}
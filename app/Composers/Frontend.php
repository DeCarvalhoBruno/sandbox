<?php namespace App\Composers;

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
        $this->addVarsToView($data, $view);
    }
}
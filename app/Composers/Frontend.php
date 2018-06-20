<?php namespace App\Composers;

class Frontend extends Composer
{
    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $data = [
            'title' => trans(sprintf('titles.%s',str_replace('.', '_', $view->getName())))
        ];
        $this->addVarsToView($data, $view);
    }
}
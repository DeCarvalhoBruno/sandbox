<?php namespace App\Composers\Frontend;

use App\Composers\Composer;

class Settings extends Composer
{
    public function compose($view)
    {
        $viewEx = explode('.', $view->getName());
        $this->addVarsToView(['viewName' => array_pop($viewEx)], $view);
    }

}
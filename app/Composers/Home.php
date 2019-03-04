<?php namespace App\Composers;

use App\Facades\JavaScript;

class Home extends Composer
{
    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {

        $data = ['meta_description' => \Cache::get('site_description')];
        $this->addVarsToView($data, $view);
    }
}
<?php namespace App\Composers;

use App\Facades\JavaScript;

class Admin extends Composer
{

    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        JavaScript::putArray([
            'appName' => config('app.name'),
            'locale' => app()->getLocale(),
            'user' => auth()->user()
        ]);

        JavaScript::bindJsVariablesToView();
    }


}
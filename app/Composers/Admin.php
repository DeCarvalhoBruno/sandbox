<?php namespace App\Composers;

use App\Facades\JavaScript;
use App\Models\User;

class Admin extends Composer
{

    /**
     * @param \Illuminate\View\View $view
     */
    public function compose($view)
    {
        $tmp = auth()->user();
        $user = null;
        if ($tmp instanceof User) {
            $tmp->setSubscribedNotifications();
            $user = $tmp->only(['username', 'system_events_subscribed']);
        }
        JavaScript::putArray([
            'appName' => config('app.name'),
            'locale' => app()->getLocale(),
            'user' => $user
        ]);

        JavaScript::bindJsVariablesToView();
    }


}
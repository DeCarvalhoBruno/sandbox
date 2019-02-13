<?php namespace App\Listeners;

use App\Jobs\UpdateOnUserLogin;
use Illuminate\Auth\Events\Login;

class UserLogin extends Listener
{
    /**
     * @param \Illuminate\Auth\Events\Login $event
     */
    public function handle(Login $event)
    {
        $this->dispatch(new UpdateOnUserLogin(
            $event->guard, $event->user, $event->remember
        ));
    }
}
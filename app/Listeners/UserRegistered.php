<?php namespace App\Listeners;

use App\Emails\User\Welcome as WelcomeEmail;
use App\Events\UserRegistered as UserRegisteredEvent;

class UserRegistered extends Listener
{
    /**
     * Deleting all permissions and re-adding them including newly added/removed users
     *
     * @param \App\Events\UserRegistered $event
     * @return void
     */
    public function handle(UserRegisteredEvent $event)
    {
        $this->dispatch(
            new \App\Jobs\SendMail(
                new WelcomeEmail((object)['user'=>$event->getUser()])
            )
        );
    }

}
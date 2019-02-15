<?php namespace App\Listeners;

use App\Emails\User\Welcome as WelcomeEmail;
use App\Events\UserRegistered as UserRegisteredEvent;
use App\Jobs\SendMail;

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
            new SendMail(
                new WelcomeEmail([
                    'user' => $event->getUser(),
                    'activation_token' => $event->getToken()
                ])
            )
        );
    }

}
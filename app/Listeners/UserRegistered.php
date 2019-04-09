<?php namespace App\Listeners;

use App\Jobs\UpdateUserElasticsearch;
use Naraki\Mail\Emails\User\Welcome as WelcomeEmail;
use App\Events\UserRegistered as UserRegisteredEvent;
use Naraki\Mail\Jobs\SendMail;
use Naraki\Media\Jobs\CreateAvatar;

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
        CreateAvatar::withChain([
            new UpdateUserElasticsearch(
                UpdateUserElasticsearch::WRITE_MODE_CREATE,
                $event->getUser()->getKey()
            )
        ])->dispatch($event->getUser()->getAttribute('username'),
            $event->getUser()->getAttribute('full_name'));
    }

}
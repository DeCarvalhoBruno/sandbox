<?php

namespace App\Listeners;

use App\Events\UserSubscribedToNewsletter as SubscribedEvent;
use App\Jobs\SubscribeToNewsletter;

class UserSubscribedToNewsletter extends Listener
{

    /**
     * Handle the event.
     *
     * @param  \App\Events\UserSubscribedToNewsletter  $event
     * @return void
     */
    public function handle(SubscribedEvent $event)
    {
        $this->dispatch(new SubscribeToNewsletter($event->getInput()));
    }
}

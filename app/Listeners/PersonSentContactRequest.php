<?php namespace App\Listeners;

use App\Emails\Frontend\Contact;
use App\Events\PersonSentContactRequest as ContactRequestEvent;
use App\Jobs\SendMail;

class PersonSentContactRequest extends Listener
{
    /**
     *
     * @param \App\Events\PersonSentContactRequest $event
     * @return void
     */
    public function handle(ContactRequestEvent $event)
    {
        $this->dispatch(
            new SendMail(
                new Contact([
                    'contact_email' => $event->getContactEmail(),
                    'contact_subject' => $event->getContactSubject(),
                    'message_body'=>$event->getMessageBody()
                ]),
                SendMail::DRIVER_SMTP
            )
        );
    }

}
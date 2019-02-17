<?php namespace App\Listeners;

use App\Contracts\Models\System as SystemProvider;
use App\Emails\Frontend\Contact;
use App\Events\PersonSentContactRequest as ContactRequestEvent;
use App\Jobs\SendMail;
use App\Models\System\SystemEvent;

class PersonSentContactRequest extends Listener
{
    /**
     * @var \App\Contracts\Models\System|\App\Support\Providers\System
     */
    private $systemRepo;

    /**
     *
     * @param \App\Contracts\Models\System|\App\Support\Providers\System $systemRepo
     */
    public function __construct(SystemProvider $systemRepo)
    {
        $this->systemRepo = $systemRepo;
    }

    /**
     *
     * @param \App\Events\PersonSentContactRequest $event
     * @return void
     */
    public function handle(ContactRequestEvent $event)
    {
        $data = [
            'contact_email' => $event->getContactEmail(),
            'contact_subject' => $event->getContactSubject(),
            'message_body' => $event->getMessageBody()
        ];
        $this->dispatch(
            new SendMail(
                new Contact($data)
            )
        );
        $this->systemRepo->log()->log(SystemEvent::CONTACT_FORM_MESSAGE, $data);
    }

}
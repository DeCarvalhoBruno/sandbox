<?php namespace App\Listeners;

use Naraki\System\Contracts\System as SystemProvider;
use Naraki\Mail\Emails\Frontend\Contact;
use App\Events\PersonSentContactRequest as ContactRequestEvent;
use Naraki\Mail\Jobs\SendMail;
use Naraki\System\Models\SystemEvent;

class PersonSentContactRequest extends Listener
{
    /**
     * @var \Naraki\System\Contracts\System|\App\Support\Providers\System
     */
    private $systemRepo;

    /**
     *
     * @param \Naraki\System\Contracts\System|\App\Support\Providers\System $systemRepo
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
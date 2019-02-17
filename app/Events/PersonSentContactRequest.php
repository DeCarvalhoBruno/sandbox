<?php namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PersonSentContactRequest extends Event implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    private $contactEmail;
    /**
     * @var string
     */
    private $contactSubject;
    /**
     * @var string
     */
    private $messageBody;

    /**
     *
     * @param string $email
     * @param string $subject
     * @param string $messageBody
     */
    public function __construct($email, $subject, $messageBody)
    {
        $this->contactEmail = $email;
        $this->contactSubject = $subject;
        $this->messageBody = $messageBody;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('general');

    }

    public function broadcastAs()
    {
        return 'message.contact';
    }

    public function broadcastWith()
    {
        return [
            'email' => $this->contactEmail,
            'subject' => $this->contactSubject,
            'body' => $this->messageBody
        ];
    }

    /**
     * @return string
     */
    public function getContactEmail(): string
    {
        return $this->contactEmail;
    }

    /**
     * @return string
     */
    public function getContactSubject(): string
    {
        return $this->contactSubject;
    }

    /**
     * @return string
     */
    public function getMessageBody(): string
    {
        return $this->messageBody;
    }


}
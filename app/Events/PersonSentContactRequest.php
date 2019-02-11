<?php namespace App\Events;

class PersonSentContactRequest extends Event
{
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
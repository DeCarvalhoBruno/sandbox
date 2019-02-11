<?php namespace App\Emails\Frontend;

use App\Emails\Email;

class Contact extends Email
{
    protected $viewName = 'emails.website.contact';

    public function prepareViewData()
    {
        $this->view = [
            'title' => trans('email.contact.title'),
            'subject' => sprintf('[%s] %s', env('APP_NAME'), trans('email.contact.email_subject')),
            'contact_email' => $this->data->contact_email,
            'contact_subject' => $this->data->contact_subject,
            'message_body' => $this->data->message_body,
            'recipient_email' => env('MAIL_FROM_ADDRESS'),
            'recipient_name' => env('MAIL_FROM_NAME'),
        ];
    }

    /**
     * @param \Illuminate\Mail\Message $message
     */
    public function message($message)
    {
        $message->subject($this->view['subject']);
        $message->from($this->from, $this->fromName);
        $message->to($this->view['recipient_email'], $this->view['recipient_name']);
        $message->replyTo($this->from, $this->fromName);
    }

}

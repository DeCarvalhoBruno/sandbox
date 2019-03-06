<?php namespace App\Emails\Frontend;

use App\Contracts\Mailer;
use App\Emails\Email;

class Contact extends Email
{
    protected $taskedMailer = Mailer::DRIVER_SMTP;
    protected $viewName = 'emails.website.contact';

    public function prepareViewData()
    {
        $this->viewData->add([
            'title' => trans('email.contact.title'),
            'subject' => sprintf(
                '[%s] %s', config('app.name'), trans('email.contact.email_subject')
            ),
            'contact_email' => $this->data->contact_email,
            'contact_subject' => $this->data->contact_subject,
            'message_body' => $this->data->message_body,
            'recipient_email' => $this->config->get('from.address'),
            'recipient_name' => $this->config->get('from.name')
        ]);
    }

}

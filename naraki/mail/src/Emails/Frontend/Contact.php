<?php namespace Naraki\Mail\Emails\Frontend;

use Naraki\Mail\Contracts\Mailer;
use Naraki\Mail\Emails\Email;

class Contact extends Email
{
    protected $taskedMailer = Mailer::DRIVER_SMTP;
    protected $viewName = 'mail::contact';

    public function prepareViewData()
    {
        $this->viewData->add([
            'title' => trans('email.contact.title'),
            'subject' => trans('email.contact.email_subject', ['app_name' => config('app.name')]),
            'recipient_email' => $this->config->get('from.address'),
            'recipient_name' => $this->config->get('from.name')
        ]);
    }

}

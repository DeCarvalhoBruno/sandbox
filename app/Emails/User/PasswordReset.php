<?php namespace App\Emails\User;

use App\Contracts\Mailer;
use App\Emails\Email;

class PasswordReset extends Email
{
    protected $taskedMailer = Mailer::DRIVER_SMTP;
    protected $viewName = 'emails.website.password_reset';

    public function prepareViewData()
    {
        parent::prepareViewData();
        $this->viewData->add([
            'title' => trans('email.password_reset.title'),
            'subject' => trans(
                'email.password_reset.subject',
                ['app_name' => config('app.name')]
            ),
            'token' => $this->data->token,
            'email' => $this->data->user->getAttribute('email')
        ]);
    }
}

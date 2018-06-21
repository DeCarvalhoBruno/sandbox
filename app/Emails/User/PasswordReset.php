<?php namespace App\Emails\User;

use App\Emails\Email;

class PasswordReset extends Email
{
    protected $viewName = 'emails.website.password_reset';

    public function prepareViewData()
    {
        $this->view = [
            'title' => trans('email.password_reset.title'),
            'subject' => trans('email.password_reset.subject'),
            'email' => $this->data->user->getAttribute('email'),
            'user_name' => $this->data->user->getFullname(),
            'token' => $this->data->token
        ];
    }

}

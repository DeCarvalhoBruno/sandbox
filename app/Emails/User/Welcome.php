<?php namespace App\Emails\User;

use App\Emails\Email;

class Welcome extends Email
{
    protected $viewName = 'emails.website.welcome';

    public function prepareViewData()
    {
        $this->view = [
            'title' => trans('email.welcome.title'),
            'subject' => trans('email.welcome.subject', ['app_name' => config('app.name')]),
            'recipient_email' => $this->data->user->getAttribute('email'),
            'recipient_name' => $this->data->user->getFullname(),
            'activation_token' => $this->data->activation_token
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

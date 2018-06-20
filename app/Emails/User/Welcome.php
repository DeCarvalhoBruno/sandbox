<?php namespace App\Emails\User;

use App\Emails\Email;

class Welcome extends Email
{
    protected $viewName = 'emails.users.welcome';

    public function prepareViewData()
    {
        $this->view = [
            'title' => trans('email.welcome.title'),
            'subject' => trans('email.welcome.subject'),
            'email' => $this->data->user->getAttribute('email'),
            'user_name' => $this->data->user->getFullname(),
            'activation_token'=>$this->data->activation_token
        ];
    }

    /**
     * @param \Illuminate\Mail\Message $message
     */
    public function message($message)
    {
        $message->subject($this->view['subject']);
        $message->from($this->from, $this->fromName);
        $message->to($this->data->user->getAttribute('email'), $this->data->user->getFullname());
        $message->replyTo($this->from, $this->fromName);
    }

}

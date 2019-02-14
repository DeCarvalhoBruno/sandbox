<?php namespace App\Support\Email;

use App\Contracts\Mailer;
use App\Emails\Email;

class LaravelMailer implements Mailer
{

    /**
     * @param \App\Emails\Email $email
     * @return void
     */
    public static function send(Email $email)
    {
        \Mail::send($email->getViewName(), $email->getViewData(), function ($message) use ($email) {
            return call_user_func([$email, 'fillMessage'], $message);
        });
    }

}
<?php namespace App\Contracts;

use App\Emails\Email;

interface Mailer
{
    const DRIVER_SMTP = 1;
    const DRIVER_MAILGUN = 2;

    public static function send(Email $email);

}
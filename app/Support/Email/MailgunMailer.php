<?php namespace App\Support\Email;

use App\Contracts\Mailer;
use App\Emails\Email;
use Illuminate\Support\Collection;
use Mailgun\Mailgun;
use Mailgun\Message\MessageBuilder;

class MailgunMailer implements Mailer
{
    /**
     * @var \Mailgun\Mailgun
     */
    private $mailgun;
    /**
     * @var \Illuminate\Contracts\View\Factory
     */
    private $view;
    /**
     * @var Message
     */
    private $message;
    /**
     * @var \Illuminate\Support\Collection
     */
    private $config;

    /**
     *
     * @param \Illuminate\Support\Collection $config
     */
    private function __construct(Collection $config)
    {
        $this->config = $config;
        $this->mailgun = static::make($config->get('private_key'));
        $this->view = app()->make('view');
    }

    public static function make($privateKey)
    {
        return Mailgun::create($privateKey);
    }

    /**
     * @param \App\Emails\Email $email
     * @return \Mailgun\Model\Message\SendResponse
     */
    public static function send(Email $email)
    {
        $mailer = new static($email->getConfig());
        $mailer->message = new Message(new MessageBuilder(), $mailer->config);
        call_user_func([$email, 'fillMessage'], $mailer->message);
        $mailer->renderBody($email->getViewName(), $email->getViewData());

        return $mailer->mailgun->messages()->send(
            $email->config('domain'),
            $mailer->message->getMessage()
        );
    }

    /**
     *
     * @param string|array $view
     * @param array $data
     */
    private function renderBody($view, array $data)
    {
        $data['message'] = $this->message;
        $this->message->builder()->setHtmlBody(
            $this->view->make($view, $data)->render()
        );
    }

}
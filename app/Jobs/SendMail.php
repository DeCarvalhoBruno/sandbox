<?php namespace App\Jobs;

use App\Contracts\Mailer;
use App\Emails\Email;
use App\Support\Email\LaravelMailer;
use App\Support\Email\MailgunMailer;

class SendMail extends Job
{
    public $queue = 'mail';
    private $email;
    private $driver;

    /**
     * Create a new job instance.
     *
     * @param $email \App\Emails\Email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
        $this->driver = $email->getTaskedMailer();
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        parent::handle();
        try {
            if ($this->driver === Mailer::DRIVER_SMTP) {
                LaravelMailer::send($this->email);
            } else {
                $response = MailgunMailer::send($this->email);
            }
            $this->delete();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
            $this->release(60);
        }
    }

    public function getEmail()
    {
        return $this->email;
    }
}
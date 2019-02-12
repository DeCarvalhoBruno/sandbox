<?php namespace App\Jobs;

use App\Emails\Email;

class SendMail extends Job
{
//    public $queue = 'mail';
    private $email;
    private $driver;
    const DRIVER_SMTP = 1;
    const DRIVER_MAILGUN = 2;

    /**
     * Create a new job instance.
     *
     * @param $email \App\Emails\Email
     * @param int $driver
     */
    public function __construct(Email $email, $driver = self::DRIVER_MAILGUN)
    {
        $this->email = $email;
        $this->driver = $driver;
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
            if ($this->driver === self::DRIVER_MAILGUN) {
                $this->email->sendMailgun();
            } else {
                $this->email->send();
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
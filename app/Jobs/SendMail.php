<?php namespace App\Jobs;

use App\Emails\Email;

class SendMail extends Job
{
//    public $queue = 'mail';
    private $email;

    /**
     * Create a new job instance.
     *
     * @param $email \App\Emails\Email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \App\Exceptions\EmailException
     */
    public function handle()
    {
        if ($this->attempts() > 3) {
            $this->delete();
        }
        try {
            $this->email->send();
            $this->delete();
        } catch (\Exception $e) {
            \Log::critical($e->getMessage(),['trace'=>$e->getTraceAsString()]);
//            app('bugsnag')->notifyException($e, ['mailData'=>$this->email->getData()], "error");
            $this->release(60);
        }
    }
    
    public function getEmail(){
        return $this->email;
    }
}
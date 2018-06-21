<?php namespace App\Console\Commands;

use App\Emails\User\Welcome as WelcomeEmail;
use App\Jobs\SendMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Symfony\Component\Console\Input\InputOption;

class SendTestEmail extends Command
{
    use DispatchesJobs;

    /**
     * The console command name.
     *
     * @var    string
     */
    protected $name = 'mail:test';

    /**
     * The console command description.
     *
     * @var    string
     */
    protected $description = 'Send email for testing purposes';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array(
                't',
                't',
                InputOption::VALUE_REQUIRED,
                'Type of email to send ("welcome", etc.)'
            ),
        );
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $type = $this->option('t');

        call_user_func(['self',$type]);
    }

    public function welcome()
    {
        $data = [
            'user' => (new User())->newQuery()->where('users.user_id', '=', 2)->first(),
            'activation_token' => '123456'
        ];
        $this->dispatch(new SendMail(new WelcomeEmail($data)));
        $this->finishDisplay();

    }

    public function __call($method, $args)
    {
        $this->comment('**********************************************');
        $this->info(sprintf('%s is not a valid email type', $method));
        $this->comment('**********************************************');
    }

    public function finishDisplay()
    {
        $this->info('the e-mail event was dispatched');

    }

}
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
                'Name of the email class to use to send the email'
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
        $class = $this->option('t');
        if ($class) {
            $fullPath = sprintf('\App\Emails\%s', $class);
            if (class_exists($fullPath)) {
                $data = [
                    'user' => (new User())->newQuery()->where('users.user_id', '=', 2)->first(),
                    'activation_token' => '123456'
                ];
                $this->dispatch(new SendMail(new WelcomeEmail((object)$data)));
            } else {
                $this->info(sprintf('%s doesn\'t exist', $fullPath));
            }
        }
    }

}
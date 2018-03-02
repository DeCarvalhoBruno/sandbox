<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UpdatePermissions extends Command
{
    use DispatchesJobs;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update permissions for the application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->dispatch(new \App\Jobs\UpdatePermissions);
        $this->info('The update permissions job was dispatched');
    }
}

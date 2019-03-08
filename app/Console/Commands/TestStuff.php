<?php namespace App\Console\Commands;


use App\Support\Database\ElasticSearch\Facades\ElasticSearchIndex;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class TestStuff extends Command
{
    use DispatchesJobs;
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to perform random tests during dev.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $client = ElasticSearchIndex::getX();
        dd($client);

    }
}

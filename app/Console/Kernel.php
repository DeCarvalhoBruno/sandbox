<?php namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\ConvertLangFilesToJs::class,
        Commands\CreateRootAssetDirectories::class,
        Commands\GenerateLangFiles::class,
        Commands\Maintenance::class,
        Commands\TestStuff::class
    ];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }
}

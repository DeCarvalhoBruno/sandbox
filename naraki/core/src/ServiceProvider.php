<?php namespace Naraki\Core;

use Naraki\Core\Contracts\RawQueries;
use Naraki\Core\Support\Viewable\View;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    protected $commands = [
        Commands\ConvertLangFilesToJs::class,
        Commands\CreateRootAssetDirectories::class,
        Commands\GenerateLangFiles::class,
        Commands\Maintenance::class,
        \App\Console\TestStuff::class
    ];

    private $routeSets = [
        Routes\Ajax::class,
        Routes\Admin::class,
        Routes\Frontend::class,
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function boot()
    {
        $this->registerComposers();
        $this->registerCommands();
        $router = $this->app->make(Router::class);
        foreach ($this->routeSets as $binder) {
            $this->app->make($binder)->bind($router);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $dbDefaultEngine = ucfirst(config('database.default'));
        $this->app->bind(
            RawQueries::class,
            sprintf('\\Naraki\\Core\\Support\\Database\\%sRawQueries', $dbDefaultEngine)
        );

        $this->app->singleton(\CyrildeWit\EloquentViewable\Contracts\View::class, View::class);
    }

    /**
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    private function registerComposers()
    {
        $this->app->make('view')->composer('admin.default', Composers\Admin::class);
        $this->app->make('view')->composer(
            [
                'frontend.site.settings.panes.profile',
                'frontend.site.settings.panes.account'
            ],
            Composers\Frontend\Profile::class
        );
        $this->app->make('view')->composer(
            'frontend.site.settings.panes.*',
            Composers\Frontend\Settings::class
        );
        $this->app->make('view')->composer([
            'frontend.auth.*',
            'frontend.site.*',
            'frontend.errors.*',
            'blog::*'
        ], Composers\Frontend::class);
        $this->app->make('view')->composer([
            'frontend.site.home',
        ], Composers\Frontend\Home::class);

    }

    private function registerCommands()
    {
        $commands = [];
        foreach ($this->commands as $command) {
            $class = new $command();
            $name = sprintf('command.naraki.%', strtolower(get_class($class)));

            $this->app->singleton($name, function () use ($class) {
                return $class;
            });
            $commands[] = $name;
        }
        $this->commands($commands);

    }

}
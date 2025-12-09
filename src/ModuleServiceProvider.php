<?php

namespace Havennow\LaravelModule;

use Havennow\LaravelModule\Console\Commands\MakeModule;
use Havennow\LaravelModule\Contracts\LoaderInterface;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        $this->publishes([
            __DIR__.'/../config/modules.php' => config_path('modules.php'),
        ], 'config');

        $loader = $this->app->make(LoaderInterface::class);
        $loader->bootstrap();

        /**
         * Register commands, so you may execute them using the Artisan CLI.
         */
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModule::class,
            ]);
        }

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/modules.php', 'modules.php'
        );

        $this->app->singleton(LoaderInterface::class, function () {
            return new Module;
        });
    }
}

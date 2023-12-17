<?php

namespace Havennow\LaravelModule;

use Havennow\LaravelModule\Contracts\LoaderInterface;
use Illuminate\Support\ServiceProvider;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     *
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/module.php' => config_path('modules.php'),
        ], 'config');

//        /**
//         * @var LoaderInterface $loader
//         */
//        $loader = $this->app->make(LoaderInterface::class);
//        $loader->bootstrap();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      //  $this->app->singleton(LoaderInterface::class, Module::class);
    }
}

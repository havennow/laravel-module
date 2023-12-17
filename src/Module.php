<?php

namespace Havennow\LaravelModule;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Havennow\LaravelModule\Contracts\LoaderInterface;
use Havennow\LaravelModule\Contracts\ModuleInterface;
use Illuminate\Contracts\Container\BindingResolutionException;
use RuntimeException;

class Module implements LoaderInterface
{
    /**
     * Laravel's container instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * ModuleLoader constructor.
     */
    public function __construct()
    {
        $this->app = app();
    }

    /**
     * @throws BindingResolutionException
     */
    public function bootstrap()
    {
        foreach ($this->getModulesList() as $module) {
            $this->enableModule($module);
        }
    }

    /**
     * Get list from all modules from a config file.
     *
     * @return string[]
     */
    protected function getModulesList()
    {
        $modules = config('modules.available', []);
        ksort($modules);

        return array_values($modules);
    }

    /**
     * Get fully qualified module class name.
     *
     * @param  string  $module
     * @return string
     */
    protected function getFullyQualifiedModuleClassName($module)
    {
        $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());

        return config('modules.namespace').'\\'.$inflector->classify($module);
    }

    /**
     * Load a single module by it's name.
     *
     * @param  string  $moduleName
     * @return bool
     *
     * @throws BindingResolutionException
     */
    protected function enableModule($moduleName)
    {
        $definition = $this->getFullyQualifiedModuleClassName($moduleName).'\\Module';

        if (! (class_exists($definition) || $this->app->bound($definition))) {
            throw new RuntimeException("Module {$definition} does'nt exist");
        }

        /** @var ModuleInterface $module */
        $module = $this->app->make($definition);

        if (! $module instanceof ModuleInterface) {
            throw new RuntimeException("Class {$definition} must implements Module interface");
        }

        $module->setApp($this->app);
        $module->setName($moduleName);

        return $module->bootstrap();
    }
}

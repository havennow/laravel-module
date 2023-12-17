<?php

namespace Havennow\LaravelModule\Contracts;

use Doctrine\Inflector\NoopWordInflector;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\View\Factory as View;
use Doctrine\Inflector\Inflector;

abstract class ModuleAbstract implements ModuleInterface
{
    /**
     * Laravel's container instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Name of the module.
     *
     * @var string
     */
    protected $name;

    /**
     * Full path of the module.
     *
     * @var string
     */
    protected $path;

    /**
     * Full namespace of the module.
     *
     * @var string
     */
    protected $namespace;

    /**
     * Namespace prefix for the module.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Should create simple {controller}/{action} routes.
     *
     * @var bool
     */
    protected $simpleRouting = false;

    /**
     * @var bool
     */
    protected $enable = true;

    /**
     * ModuleDefinition constructor.
     */
    public function __construct()
    {
    }

    /**
     * Bootstrap a new module.
     *
     * @return bool
     */
    public function bootstrap()
    {
        $this->loadHelpers();
        $this->loadRoutes();
        $this->loadViews();
    }

    /**
     * Get module name.
     *
     * @return string
     */
    protected function getName()
    {
        return $this->name;
    }

    /**
     * Get module folder full path.
     *
     * @return string
     */
    protected function getModulesFolder()
    {
        if (! $this->path) {
            $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
            $this->path = realpath(config('modules.path').'/'.$inflector->classify($this->getName()));
        }

        return $this->path;
    }

    /**
     * Get module full namespace.
     *
     * @return string
     */
    protected function getModulesNamespace()
    {
        if (! $this->namespace) {
            $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
            $this->namespace = config('modules.namespace').'\\'.$inflector->classify($this->getName());
        }

        return $this->namespace;
    }

    /**
     * Get module full namespace.
     *
     * @return string
     */
    protected function getModulesPrefix()
    {
        if (! $this->prefix) {
            $inflector = new Inflector(new NoopWordInflector(), new NoopWordInflector());
            $this->prefix = $inflector->tableize($this->getName());
        }

        return $this->prefix;
    }

    /**
     * Load helpers file if exists.
     *
     * @return void
     */
    protected function loadHelpers()
    {
        $helpersFile = $this->getModulesFolder().'/helpers.php';

        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }

    /**
     * Load routes file if exists.
     *
     * @return void
     */
    abstract function loadBefore();

    /**
     * Load routes file if exists.
     *
     * @return void
     */
    protected function loadRoutes()
    {
        /** @var Registrar $router */
        $router = $this->app->make('router');
        $namespace = $this->getModulesNamespace().'\\Controllers';

        $router->group(compact('namespace'), function () use ($router) {
            $this->bindRoutes($router);
        });
    }

    /**
     * Load views folder if exists.
     *
     * @return void
     */
    protected function loadViews()
    {
        /** @var View $view */
        $view = $this->app->make(View::class);
        $viewsFolder = realpath($this->getModulesFolder().'/Views');

        if (file_exists($viewsFolder)) {
            $view->addNamespace($this->getModulesPrefix(), $viewsFolder);
        }
    }

    /**
     * Set laravel's container instance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function setApp($app)
    {
        $this->app = $app;
    }

    /**
     * Set name of the module.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Bind application routes.
     *
     * @param Registrar $router
     * @return void
     */
    abstract public function bindRoutes(Registrar $router);


    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }
}
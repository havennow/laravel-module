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
     * @var bool
     */
    protected $enable = true;

    /**
     * @var bool
     */
    protected $view = false;

    /**
     * ModuleDefinition constructor.
     */
    public function __construct()
    {
    }

    /**
     * Bootstrap a new module.
     *
     * @return void
     */
    public function bootstrap()
    {
        $this->loadBefore();

        if (!$this->isEnable()) {
            return;
        }

        $this->loadRoutes();

        if ($this->isViewEnable()) {
            $this->loadComposers();
            $this->loadViews();
        }

        $this->loadHelpers();
    }

    /**
     * Get module name.
     *
     * @return string
     */
    protected function getName(): string
    {
        return $this->name;
    }

    /**
     * Get module folder full path.
     *
     * @return string
     */
    protected function getModulesFolder(): string
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
    protected function getModulesNamespace(): string
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
    protected function getModulesPrefix(): string
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
    protected function loadHelpers(): void
    {
        $helpersFile = $this->getModulesFolder().'/helpers.php';

        if (file_exists($helpersFile)) {
            require_once $helpersFile;
        }
    }

    /**
     * Load before code run.
     *
     * @return void
     */
    protected function loadBefore(): void
    {
        //before for example disable or enable
    }

    /**
     * @return void
     */
    protected function loadComposers(): void
    {
        // load view composer
    }

    /**
     * Load routes file if exists.
     *
     * @return void
     */
    protected function loadRoutes(): void
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
    protected function loadViews(): void
    {
        /** @var View $view */
        $view = $this->app->make(View::class);
        $viewsFolder = realpath($this->getModulesFolder().'/Views');

        if (file_exists($viewsFolder)) {
            $view->addLocation($viewsFolder);
        }
    }

    /**
     * Set laravel's container instance.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    public function setApp($app): void
    {
        $this->app = $app;
    }

    /**
     * Set name of the module.
     *
     * @param string $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Bind application routes.
     *
     * @param Registrar $router
     * @return void
     */
    abstract public function bindRoutes(Registrar $router): void;


    public function setEnable(bool $enable): void
    {
        $this->enable = $enable;
    }

    public function isEnable(): bool
    {
        return $this->enable;
    }

    public function setView(bool $enable): void
    {
        $this->view = $enable;
    }

    public function isViewEnable(): bool
    {
        return $this->view;
    }
}

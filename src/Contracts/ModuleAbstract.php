<?php

namespace Havennow\LaravelModule\Contracts;

use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\NoopWordInflector;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\View\Factory as View;

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
     * @var string
     */
    protected $routePrefix;

    /**
     * ModuleDefinition constructor.
     */
     public function __construct() {}

    /**
     * Bootstrap a new module.
     */
    public function bootstrap(): void
    {
        $this->loadBefore();

        if (! $this->isEnable()) {
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
     */
    protected function getName(): string
    {
        return $this->name;
    }

    protected function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }

    public function setRoutePrefix($routePrefix): void
    {
        $this->routePrefix = $routePrefix;
    }

    /**
     * Get module folder full path.
     */
    protected function getModulesFolder(): string
    {
        if (! $this->path) {
            $inflector = new Inflector(new NoopWordInflector, new NoopWordInflector);
            $this->path = realpath(config('modules.path').'/'.$inflector->classify($this->getName()));
        }

        return $this->path;
    }

    private static function getConfigModulesFile(?string $name = null): ?array
    {
        if (blank($name)) {
            return null;
        }

        $inflector = new Inflector(new NoopWordInflector, new NoopWordInflector);
        $file = realpath(
            config('modules.path')
            .'/'.
            $inflector->classify($name)
            .'/Config/main.php'
        );

        if (file_exists($file)) {
            return require $file;
        }

        return null;
    }

    public static function getConfigModule(?string $name = null): mixed
    {
        return self::getConfigModulesFile($name);
    }

    /**
     * Get module full namespace.
     */
    protected function getModulesNamespace(): string
    {
        if (! $this->namespace) {
            $inflector = new Inflector(new NoopWordInflector, new NoopWordInflector);
            $this->namespace = config('modules.namespace').'\\'.$inflector->classify($this->getName());
        }

        return $this->namespace;
    }

    /**
     * Get module full namespace.
     */
    protected function getModulesPrefix(): string
    {
        if (! $this->prefix) {
            $inflector = new Inflector(new NoopWordInflector, new NoopWordInflector);
            $this->prefix = $inflector->tableize($this->getName());
        }

        return $this->prefix;
    }

    /**
     * Load helpers file if exists.
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
     */
    protected function loadBefore(): void
    {
        // before for example disable or enable
    }

    protected function loadComposers(): void
    {
        // load view composer
    }

    /**
     * Load routes file if exists.
     */
    protected function loadRoutes(): void
    {
        /** @var Registrar $router */
        $router = $this->app->make('router');
        $namespace = $this->getModulesNamespace().'\\Controllers';
        $routePrefix = $this->getRoutePrefix();
        $params = ['namespace' => $namespace];

        if (filled($routePrefix)) {
            $params['prefix'] = $routePrefix;
        }

        $router->group($params, function () use ($router) {
            $this->bindRoutes($router);
        });
    }

    /**
     * Load views folder if exists.
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
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function setApp($app): void
    {
        $this->app = $app;
    }

    /**
     * Set name of the module.
     *
     * @param  string  $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * Bind application routes.
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

    public function setView($enable): void
    {
        $this->view = $enable;
    }

    public function isViewEnable(): bool
    {
        return $this->view;
    }
}

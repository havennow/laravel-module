<?php

namespace Havennow\LaravelModule\Contracts;

interface ModuleInterface
{
    /**
     * Bootstrap a new module.
     *
     * @return bool
     */
    public function bootstrap();

    /**
     * Set laravel's container instance.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    public function setApp($app);

    /**
     * Set name of the module.
     *
     * @param  string  $name
     * @return void
     */
    public function setName($name);

    /**
     * Set view enable or not
     *
     * @param  bool  $enable
     */
    public function setView($enable): void;

    /**
     * Set route prefix
     *
     * @return mixed
     */
    public function setRoutePrefix($routePrefix): void;
}

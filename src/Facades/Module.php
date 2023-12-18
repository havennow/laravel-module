<?php

namespace Havennow\LaravelModule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Havennow\LaravelModule\Module
 */
class Module extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Havennow\LaravelModule\Module::class;
    }
}

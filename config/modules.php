<?php

return [
    'enable' => env('LARAVEL_MODULE_ENABLE', true),
    'namespace' => 'App\\Modules',
    'path' => app_path('Modules'),
    'available' => [
        //  1 => ['name' => 'test', 'enable' => true, 'view_enable' => true, 'route_prefix' => 'api'],
        //  2 => ['name' => 'api', 'enable' => false]
    ],
];

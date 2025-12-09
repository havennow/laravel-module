<?php

return [
    'enable' => env('LARAVEL_MODULE_ENABLE', true),
    'namespace' => 'App\\Modules',
    'path' => app_path('Modules'),
    'available' => [
//        1 => [
//            'name' => 'test',
//            'enable' => env('TEST_LARAVEL_MODULE_ENABLE', true),
//            'view_enable' => false,
//            'route_prefix' => 'api'
//        ],
//        2 => [
//            'name' => 'api',
//            'enable' => env('API_LARAVEL_MODULE_ENABLE', true),
//            'view_enable' => false,
//            'route_prefix' => 'api'
//        ],
    ],
];

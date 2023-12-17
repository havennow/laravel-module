<?php

return [
    'enable' => env('LARAVEL_MODULE_ENABLE', true),
    'namespace' => 'App\\Modules',
    'path' => app_path('Modules'),
    'available' => [
        10 => ['name' => 'other', 'enable' => true],
        0 => ['name' => 'frontend', 'enable' => false]
    ],
];

<?php

return [
    'enable' => env('LARAVEL_MODULE_ENABLE', true),
    'namespace' => 'App\\Modules',
    'path' => app_path('Modules'),
    'available' => [
        10 => 'other',
        0 => 'frontend',
    ],
];

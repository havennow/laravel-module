<?php

return [
    'enable' => env('LARAVEL_MODULE_ENABLE', true),
    'namespace' => 'App\\Modules',
    'path' => app_path('Modules'),
    'available' => [
      //  1 => ['name' => 'test', 'enable' => true],
      //  2 => ['name' => 'frontend', 'enable' => false]
    ],
];

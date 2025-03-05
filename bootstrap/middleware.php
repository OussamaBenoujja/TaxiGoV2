<?php

return [
    // Global middleware
    'web' => [
        // Web middleware...
    ],

    'api' => [
        // API middleware...
    ],

    // Named middleware
    'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
    // Add your middleware here
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    // Other middleware...
];
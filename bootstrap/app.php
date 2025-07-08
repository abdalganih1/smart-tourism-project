<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Define global middleware that applies to all routes (less common)
        // $middleware->append(MyGlobalMiddleware::class);

        // Define middleware aliases (if you want to use a shorthand like 'auth' or 'can')
        // These are often already defined by Breeze, etc.
        // $middleware->alias([
        //     'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        //     'can' => \Illuminate\Auth\Middleware\Authorize::class,
        //      // ... other aliases
        // ]);

        // Define middleware groups (this replaces $middlewareGroups in old Kernel.php)
        // Add your SetLocale middleware to the 'web' group
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);

        // Optional: Add your SetLocale middleware to the 'api' group if needed
        // $middleware->appendToGroup('api', \App\Http\Middleware\SetLocale::class);

        // You can also modify existing groups or define new ones if necessary
        // $middleware->replaceInGroup('web', \Illuminate\Session\Middleware\StartSession::class, \App\Http\Middleware\CustomStartSession::class);
        // $middleware->appendGroup('my_custom_group', [
        //     \App\Http\Middleware\MyCustomMiddleware::class,
        // ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
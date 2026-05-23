<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Harus sebelum auth.session — kalau tidak, User di-load dari hrd_central.users
        $middleware->web(
            remove: ['auth.session'],
            append: [
                \App\Http\Middleware\InitializeTenancyFromSession::class,
                'auth.session',
            ],
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

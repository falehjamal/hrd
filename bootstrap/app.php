<?php

use App\Http\Middleware\EnsureHrUser;
use App\Http\Middleware\EnsureLinkedEmployee;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\InitializeTenancyFromSession;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/platform.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Harus sebelum auth.session — kalau tidak, User di-load dari hrd_central.users
        $middleware->web(
            remove: ['auth.session'],
            append: [
                InitializeTenancyFromSession::class,
                EnsureTenantIsActive::class,
                'auth.session',
            ],
        );

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('platform', 'platform/*')) {
                return route('platform.login');
            }

            return route('login');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            if ($request->is('platform', 'platform/*')) {
                return route('platform.dashboard');
            }

            return route('dashboard');
        });

        $middleware->alias([
            'employee.linked' => EnsureLinkedEmployee::class,
            'hr.user' => EnsureHrUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

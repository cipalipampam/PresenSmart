<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        // Web admins authenticate through their session, while the mobile app
        // authenticates with a Sanctum bearer token. The web middleware starts
        // the session required by both flows.
        ['middleware' => ['web', 'auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Broadcast authorization does not change server state. It must accept
        // the mobile app's bearer token, which has no browser CSRF token.
        $middleware->validateCsrfTokens(except: ['broadcasting/auth']);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // En desarrollo, Expose puede terminar HTTPS y reenviar la petición
        // a Laravel por HTTP. Confiamos en los encabezados reenviados para que
        // Laravel reconozca correctamente el esquema HTTPS y genere sus URLs.
        if (env('APP_ENV', 'production') === 'local') {
            $middleware->trustProxies(
                at: '*',
                headers: Request::HEADER_X_FORWARDED_FOR |
                    Request::HEADER_X_FORWARDED_HOST |
                    Request::HEADER_X_FORWARDED_PORT |
                    Request::HEADER_X_FORWARDED_PROTO
            );
        }

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'personal.activo' => \App\Http\Middleware\PersonalActivoMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckUserBranch;
use App\Http\Middleware\Cors;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
            'api.role' => \App\Http\Middleware\ApiRoleMiddleware::class,
            'branch' => CheckUserBranch::class,
            'cors' => Cors::class,
        ]);

        // Web-only middleware
        $middleware->web(append: [
            SetLocale::class,
        ]);

        // CORS cho API
        $middleware->api(prepend: [
            Cors::class,
        ]);

        $middleware->validateCsrfTokens(except: ['api/*'])
            ->trustProxies(at: ['*']);

        $middleware->group('api', [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {})->create();

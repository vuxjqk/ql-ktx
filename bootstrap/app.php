<?php

use App\Http\Middleware\CheckRole;
use App\Http\Middleware\CheckUserBranch;
use App\Http\Middleware\Cors;
use App\Http\Middleware\ForceCorrectUrl;
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
            'branch' => CheckUserBranch::class,
            'cors' => Cors::class,
        ]);
        $middleware->web(append: [
            SetLocale::class,
        ]);
        $middleware->api(prepend: [
            Cors::class,
        ]);
        $middleware->append(ForceCorrectUrl::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

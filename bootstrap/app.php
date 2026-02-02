<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Configure route model binding for Material model
            Route::model('material', \App\Models\Material::class);
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Exclude Telegram webhook from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'telegram/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

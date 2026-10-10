<?php

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
        // Remove upload size limit validation to allow large file uploads
        $middleware->remove(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
        
        $middleware->alias([
            'superadmin' => App\Http\Middleware\EnsureSuperAdmin::class,
            'officer' => App\Http\Middleware\EnsureOfficer::class,
        ]);

        // Configure authentication redirects
        $middleware->redirectGuestsTo(fn() => route('admin.login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

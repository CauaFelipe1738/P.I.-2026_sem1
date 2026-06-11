<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Registrando o apelido 'admin' para o middleware
        $middleware->alias([
            'admin' => AdminMiddleware::class,
        ]);

        // Solução temporária que ignora o CSRF nestas URLs para usar como API
        $middleware->preventRequestForgery(except: [
            'login',
            'admin/*'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

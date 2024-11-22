<?php

use App\Http\Middleware\LogAcessoMiddlaware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // 'auth' => \App\Http\Middleware\Autheticate::class,
            'log.acesso' => \App\Http\Middleware\LogAcessoMiddlaware::class,
            'autenticacao' => \App\Http\Middleware\AutenticacaoMiddleware::class
        ]);

        //adicionando middlaware a todas as rotas sem necessidade de chamar pontualmente em cada
        $middleware->append(LogAcessoMiddlaware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

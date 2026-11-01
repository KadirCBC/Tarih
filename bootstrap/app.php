<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
            \App\Http\Middleware\AntiScrapeBotProtectionMiddleware::class,
        ]);

        $middleware->api(append: [
            \App\Http\Middleware\SetLocaleMiddleware::class,
        ]);

        $middleware->alias([
            'quota.scan' => \App\Http\Middleware\CheckScanQuota::class,
            'locale' => \App\Http\Middleware\SetLocaleMiddleware::class,
            'antiscrape' => \App\Http\Middleware\AntiScrapeBotProtectionMiddleware::class,
            'watermark' => \App\Http\Middleware\DynamicWatermarkMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();


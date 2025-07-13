<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Tenancy middlewares
            'tenancy.init'   => \Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
            'tenancy.block'  => \Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains::class,
        ]);
        // Оборачиваем MoonShine роутинг в tenancy
        $middleware->appendToGroup('moonshine', [
            'tenancy.init',
            'tenancy.block',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

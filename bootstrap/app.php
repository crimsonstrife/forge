<?php

use App\Http\Middleware\EnsureSupportIdentity;
use App\Http\Middleware\SetPermissionsTeamContext;
use App\Http\Middleware\VerifyIngestKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(__DIR__.'/../routes/channels.php')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(SetPermissionsTeamContext::class);
        $middleware->alias([
            'support.identity' => EnsureSupportIdentity::class,
            'ingest.key' => VerifyIngestKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

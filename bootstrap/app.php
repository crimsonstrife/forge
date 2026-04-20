<?php

use App\Http\Middleware\AllowPublicEmbed;
use App\Http\Middleware\EnsureRegistrationIsEnabled;
use App\Http\Middleware\EnsureSupportIdentity;
use App\Http\Middleware\SetPermissionsTeamContext;
use App\Http\Middleware\VerifyIngestKey;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

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
            'support.identity'  => EnsureSupportIdentity::class,
            'ingest.key'        => VerifyIngestKey::class,
            'auth.registration' => EnsureRegistrationIsEnabled::class,
            // Validates client credentials tokens (machine-to-machine OAuth2)
            'client'            => \Laravel\Passport\Http\Middleware\CheckToken::class,
        ]);
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
        ]);
        $middleware->group('embed', [
            AllowPublicEmbed::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            \Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class,
            \Livewire\Exceptions\RootTagMissingFromViewException::class,
        ]);

        $exceptions->dontReportWhen(function (\Throwable $e) {
            if (! $e instanceof \TypeError) {
                return false;
            }

            $msg = $e->getMessage();

            return str_contains($msg, 'Filament\Notifications\Collection')
                || str_contains($msg, 'Filament\Notifications\Livewire\Notifications::$isFilamentNotificationsComponent');
        });
    })->create();

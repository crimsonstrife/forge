<?php

use App\Http\Middleware\AllowPublicEmbed;
use App\Http\Middleware\EnsureFeedbackIdentity;
use App\Http\Middleware\EnsureOptionalFeedbackIdentity;
use App\Http\Middleware\EnsureRegistrationIsEnabled;
use App\Http\Middleware\EnsureSupportIdentity;
use App\Http\Middleware\SetPermissionsTeamContext;
use App\Http\Middleware\VerifyIngestKey;
use App\Http\Middleware\VerifySentryHookSignature;
use App\Http\Middleware\VerifySentryInstallation;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Laravel\Passport\Http\Middleware\CheckToken;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Livewire\Exceptions\RootTagMissingFromViewException;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;

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
            'feedback.identity' => EnsureFeedbackIdentity::class,
            'feedback.identity.optional' => EnsureOptionalFeedbackIdentity::class,
            'ingest.key' => VerifyIngestKey::class,
            'sentry.hook' => VerifySentryHookSignature::class,
            'sentry.installation' => VerifySentryInstallation::class,
            'auth.registration' => EnsureRegistrationIsEnabled::class,
            // Validates client credentials tokens (machine-to-machine OAuth2)
            'client' => CheckToken::class,
        ]);
        $middleware->group('api', [
            EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            SubstituteBindings::class,
        ]);
        $middleware->group('embed', [
            AllowPublicEmbed::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontReport([
            CannotUpdateLockedPropertyException::class,
            RootTagMissingFromViewException::class,
        ]);

        $exceptions->dontReportWhen(function (Throwable $e) {
            if (! $e instanceof TypeError) {
                return false;
            }

            $msg = $e->getMessage();

            return str_contains($msg, 'Filament\Notifications\Collection')
                || str_contains($msg, 'Filament\Notifications\Livewire\Notifications::$isFilamentNotificationsComponent');
        });
    })->create();

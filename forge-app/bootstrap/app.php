<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\Cookies;
use App\Http\Middleware\CsrfVerification;
use App\Http\Middleware\TrustHost;
use App\Http\Middleware\TrustProxy;
use App\Http\Middleware\TrimStrings;
use App\Http\Middleware\Maintenance;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Auth\Middleware\Authorize;
use App\Http\Middleware\Redirect;
use App\Http\Middleware\SignatureValidation;
use App\Http\Middleware\EnsureMockMode;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\PermissionSet;
use App\Http\Middleware\TrackProjectView;
use App\Http\Middleware\CheckProjectPermission;
use App\Http\Middleware\CheckTeamPermission;

/**
 * Configures the application and returns an instance of the Application class.
 *
 * @param string $basePath The base path of the application.
 * @return Application The configured application instance.
 */
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(TrustHost::class);
        $middleware->append(TrustProxy::class);
        $middleware->append(HandleCors::class);
        $middleware->append(Maintenance::class);
        $middleware->append(ValidatePostSize::class);
        $middleware->append(TrimStrings::class);
        $middleware->append(ConvertEmptyStringsToNull::class);
        $middleware->appendToGroup('web', [
            Cookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            CsrfVerification::class,
            SubstituteBindings::class,
            PermissionSet::class,
        ]);
        $middleware->appendToGroup('api', [
            'throttle:api',
            SubstituteBindings::class,
            PermissionSet::class,
        ]);
        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'auth.session' => AuthenticateSession::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => Redirect::class,
            'password.confirm' => RequirePassword::class,
            'signed' => SignatureValidation::class,
            'throttle' => ThrottleRequests::class,
            'verified' => EnsureEmailIsVerified::class,
            'auth.permissionSet' => PermissionSet::class,
            'ensureMockMode' => EnsureMockMode::class,
            'track.project.view' => TrackProjectView::class,
            'project.permission' => CheckProjectPermission::class,
            'team.permission' => CheckTeamPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

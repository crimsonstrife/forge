<?php

namespace App\Providers;

use App\Contracts\RepositoryProviderInterface;
use App\Models\Comment;
use App\Models\Issue;
use App\Models\PermissionSet;
use App\Models\PersonalAccessToken;
use App\Models\Project;
use App\Models\Role;
use App\Observers\CommentObserver;
use App\Observers\IssueObserver;
use App\Observers\PermissionSetObserver;
use App\Observers\ProjectObserver;
use App\Observers\RoleObserver;
use App\Services\Feedback\FeedbackSessionService;
use App\Session\ResilientSessionManager;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Cache\Events\CacheFailedOver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;
use Laravel\Telescope\TelescopeServiceProvider;
use Livewire\Livewire;
use SocialiteProviders\GitHub\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(TelescopeServiceProvider::class)
            && $this->app->environment('local')
            && class_exists(\Redis::class)) {
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton(ResilientSessionManager::class, function ($app) {
            return new ResilientSessionManager($app);
        });

        $this->app->singleton(SessionManager::class, function ($app) {
            return $app->make(ResilientSessionManager::class);
        });

        $this->app->singleton('session', function ($app) {
            return $app->make(ResilientSessionManager::class);
        });

        $this->app->singleton('session.store', function ($app) {
            return $app->make('session')->driver();
        });

        $this->app->singleton(StartSession::class, function ($app) {
            return new StartSession($app->make(SessionManager::class), function () use ($app) {
                return $app->make(CacheFactory::class);
            });
        });

        $this->app->bind(RepositoryProviderInterface::class, GitHubRepositoryProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(CacheFailedOver::class, function (CacheFailedOver $event): void {
            if (! $this->shouldLogCacheFailover($event)) {
                return;
            }

            $store = $this->cacheFailoverLogStore();
            $logKey = sprintf(
                'cache:failover:%s:%s',
                $event->storeName ?? 'unknown',
                sha1($event->exception::class.'|'.$event->exception->getMessage())
            );

            try {
                $shouldLog = Cache::store($store)->add(
                    $logKey,
                    true,
                    now()->addMinutes($this->cacheFailoverLogThrottleMinutes())
                );
            } catch (Throwable) {
                $shouldLog = true;
            }

            if (! $shouldLog) {
                return;
            }

            Log::warning('Primary cache store failed over to the configured fallback store.', [
                'store' => $event->storeName,
                'fallback_store' => $this->cacheFailoverLogStore(),
                'exception' => $event->exception::class,
                'message' => $event->exception->getMessage(),
            ]);
        });

        RateLimiter::for('livewire-update', static function (Request $request) {
            $key = optional($request->user())?->getAuthIdentifier() ?? $request->ip();

            return Limit::perMinute(240)->by('lw:'.$key);
        });

        Livewire::setUpdateRoute(static fn ($handle) => Route::post('/livewire/update', $handle)
            ->middleware(['web', 'throttle:livewire-update'])
            ->name('livewire.update'));

        Auth::viaRequest('feedback-session', static function (Request $request) {
            $header = (string) $request->header('Authorization', '');
            if (! str_starts_with($header, 'Bearer ')) {
                return null;
            }

            return app(FeedbackSessionService::class)->resolveBearer(substr($header, 7), $request);
        });

        RateLimiter::for('feedback', static function (Request $request) {
            $key = $request->attributes->get('ingest_key');
            $bucket = $key?->id ?? $request->ip();

            return Limit::perMinute(120)->by('feedback:'.$bucket);
        });

        RateLimiter::for('feedback-auth', static function (Request $request) {
            $email = strtolower((string) $request->input('email', ''));

            return [
                Limit::perHour(5)->by('feedback-auth-email:'.hash('sha256', $email)),
                Limit::perHour(20)->by('feedback-auth-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('feedback-vote', static function (Request $request) {
            $header = (string) $request->header('Authorization', '');
            $identity = str_starts_with($header, 'Bearer ')
                ? app(FeedbackSessionService::class)->resolveBearer(substr($header, 7))
                : null;
            $bucket = $identity?->getAuthIdentifier() ?? $request->ip();

            return Limit::perHour(60)->by('feedback-vote:'.$bucket);
        });

        RateLimiter::for('feedback-post', static function (Request $request) {
            $header = (string) $request->header('Authorization', '');
            $identity = str_starts_with($header, 'Bearer ')
                ? app(FeedbackSessionService::class)->resolveBearer(substr($header, 7))
                : null;
            $bucket = $identity?->getAuthIdentifier() ?? $request->ip();

            return Limit::perDay(5)->by('feedback-post:'.$bucket);
        });

        RateLimiter::for('feedback-comment', static function (Request $request) {
            $header = (string) $request->header('Authorization', '');
            $identity = str_starts_with($header, 'Bearer ')
                ? app(FeedbackSessionService::class)->resolveBearer(substr($header, 7))
                : null;
            $bucket = $identity?->getAuthIdentifier() ?? $request->ip();

            return Limit::perHour(20)->by('feedback-comment:'.$bucket);
        });

        RateLimiter::for('api', static function (Request $request) {
            $key = optional($request->user())?->getAuthIdentifier()
                ?? optional($request->user())?->currentAccessToken()?->id
                ?? $request->ip();

            return Limit::perMinute(120)->by($key);
        });

        RateLimiter::for('ticket-ingest', static function (Request $request) {
            $key = $request->attributes->get('ingest_key');
            $bucket = $key?->id ?? $request->ip();

            return [Limit::perMinute(20)->by('ingest:'.$bucket)];
        });

        if ($this->app->runningInConsole()) {
            return; // do not touch URL/Request during composer/CLI
        }

        Scramble::configure()->withDocumentTransformers(function (OpenApi $doc) {
            $doc->info->title = config('app.name').' API';
            $doc->secure(SecurityScheme::http('bearer')); // default for all endpoints
        });

        Paginator::useBootstrapFive();
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        Issue::observe(IssueObserver::class);
        Project::observe(ProjectObserver::class);
        Comment::observe(CommentObserver::class);
        Role::observe(RoleObserver::class);
        PermissionSet::observe(PermissionSetObserver::class);

        Event::listen(static function (SocialiteWasCalled $event) {
            $event->extendSocialite('github', Provider::class);
            $event->extendSocialite('gitea', \SocialiteProviders\Gitea\Provider::class);
            $event->extendSocialite('gitlab', \SocialiteProviders\GitLab\Provider::class);
            $event->extendSocialite('discord', \SocialiteProviders\Discord\Provider::class);
            $event->extendSocialite('todoist', \SocialiteProviders\Todoist\Provider::class);
            $event->extendSocialite('atlassian', \SocialiteProviders\Atlassian\Provider::class);
        });
    }

    private function shouldLogCacheFailover(CacheFailedOver $event): bool
    {
        return $event->storeName === config('cache.stores.failover.primary_store');
    }

    private function cacheFailoverLogStore(): string
    {
        $store = (string) config('cache.stores.failover.fallback_store', 'file');

        return $store === 'failover' ? 'file' : $store;
    }

    private function cacheFailoverLogThrottleMinutes(): int
    {
        return max(1, (int) config('cache.stores.failover.log_throttle_minutes', 5));
    }
}

<?php

namespace App\Providers;

use App\Contracts\RepositoryProviderInterface;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // At runtime, you’ll choose based on repo->provider, so bind via a factory:
        $this->app->bind('repo-provider-factory', function ($app) {
            return static function (string $provider) use ($app) {
                return match ($provider) {
                    'github' => $app->make(GitHubRepositoryProvider::class),
                    // 'gitlab' => $app->make(GitLabRepositoryProvider::class),
                    // 'gitea'  => $app->make(GiteaRepositoryProvider::class),
                    default  => throw new RuntimeException("Unknown provider: {$provider}"),
                };
            };
        });
    }
}

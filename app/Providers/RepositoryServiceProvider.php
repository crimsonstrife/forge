<?php

namespace App\Providers;

use App\Contracts\RepositoryProviderInterface;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

/**
 * Registers a factory that returns the correct repository provider implementation
 * based on a string like 'github' | 'gitlab' | 'gitea' | 'crucible'.
 */
final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind a factory closure into the container.
        // Usage: $factory = app('repo-provider-factory'); $provider = $factory('github');
        $this->app->bind('repo-provider-factory', function ($app) {
            return function (string $provider) use ($app) {
                return match (strtolower($provider)) {
                    'github' => $app->make(GitHubRepositoryProvider::class),
                    // 'gitlab' => $app->make(\App\Providers\GitLabRepositoryProvider::class),
                    // 'gitea'  => $app->make(\App\Providers\GiteaRepositoryProvider::class),
                    // 'crucible' => $app->make(\App\Providers\CrucibleRepositoryProvider::class),
                    default => throw new \RuntimeException("Unknown repository provider: {$provider}"),
                };
            };
        });
    }
}

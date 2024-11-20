<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Bind recent collaborators to the navigation menu
        View::composer('navigation-menu', function ($view) {
            $recentCollaborators = Auth::check()
                ? Auth::user()->recentCollaborators(5) // Retrieve up to 5 collaborators
                : collect();

            $view->with('recentCollaborators', $recentCollaborators);
        });

        // Bind the user's favorite projects to the navigation menu
        View::composer('navigation-menu', function ($view) {
            $favoriteProjects = Auth::check()
                ? Auth::user()->favoriteProjects
                : collect();

            $view->with('favoriteProjects', $favoriteProjects);
        });

        // Bind the user's projects to the navigation menu
        View::composer('navigation-menu', function ($view) {
            $projects = Auth::check()
                ? Auth::user()->projects
                : collect();

            $view->with('projects', $projects);
        });

        // Boot the permission helpers for Blade
        $this->bootPermissionHelpers();
    }

    /**
     * Boot the permission helpers for Blade.
     */
    private function bootPermissionHelpers(): void
    {
        Blade::directive('canProject', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasProjectPermission({$expression})): ?>";
        });

        Blade::directive('endCanProject', function () {
            return "<?php endif; ?>";
        });

        Blade::directive('canTeam', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasTeamPermission({$expression})): ?>";
        });

        Blade::directive('endCanTeam', function () {
            return "<?php endif; ?>";
        });
    }
}

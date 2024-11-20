<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    }
}

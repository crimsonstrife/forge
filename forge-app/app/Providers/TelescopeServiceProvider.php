<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

/**
 * TelescopeServiceProvider class.
 *
 * This class is responsible for registering application services and defining the Telescope gate.
 */
class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal || $entry->isReportableException() || $entry->isFailedRequest() || $entry->isFailedJob() || $entry->isScheduledTask() || $entry->hasMonitoredTag();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            // Get users who have the access-telescope permission.
            $userModel = config('auth.providers.users.model');
            // Initiate an instance of the User model
            $user = new $userModel();

            // Create an array to hold the users who have the access-telescope permission
            $usersWithAccess = [];

            // Loop through the users and check if they have the access-telescope permission
            foreach ($users as $user) {
                if ($user->hasPermissionTo('access-telescope')) {
                    $usersWithAccess[] = $user;
                }
            }

            // Return true if the user is in the usersWithAccess array
            return in_array($user, $usersWithAccess);
        });
    }
}

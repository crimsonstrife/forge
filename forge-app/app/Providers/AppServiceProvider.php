<?php

namespace App\Providers;

use App\Settings\GeneralSettings;
use Filament\Facades\Filament;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Facades\Excel;
use App\Services\CustomValueBinder;

/**
 * AppServiceProvider class.
 *
 * This class is responsible for registering and bootstrapping any application services.
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //register services for local development
        if ($this->app->environment('local')) {
            //register Telescope
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure application
        $this->configureApp();

        // Double encode Blade directives
        Blade::directive('json', function ($expression) {
            return new HtmlString('<?php echo json_encode(' . $expression . '); ?>');
        });

        // Set the base URL if it is set in the environment
        if ($baseUrl = env('APP_URL')) {
            URL::forceRootUrl($baseUrl);
        }

        // Register the CustomValueBinder
        Excel::bindValueBinder(CustomValueBinder::class);
    }

    private function configureApp(): void
    {
        try {
            $settings = app(GeneralSettings::class);
            Config::set('app.locale', $settings->site_language ?? config('app.fallback_locale'));
            Config::set('app.name', $settings->site_name ?? env('APP_NAME'));
            Config::set('filament.brand', $settings->site_name ?? env('APP_NAME'));
            Config::set(
                'app.logo',
                $settings->site_logo ? asset('storage/' . $settings->site_logo) : asset('favicon.ico')
            );
            Config::set('filament-breezy.enable_registration', $settings->enable_registration ?? false);
            Config::set('filament-socialite.registration', $settings->enable_registration ?? false);
            Config::set('filament-socialite.enabled', $settings->enable_social_login ?? false);
            Config::set('system.login_form.is_enabled', $settings->enable_login_form ?? false);
            Config::set('services.oidc.is_enabled', $settings->enable_oidc_login ?? false);
        } catch (QueryException $e) {
            // If the environment is local, throw a warning but do nothing, otherwise, throw an error
            if ($this->app->environment('local')) {
                $this->app->make('log')->warning('GeneralSettings not found in the settings table.');
            } else {
                throw $e;
            }
        }
    }
}

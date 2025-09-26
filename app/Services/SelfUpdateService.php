<?php

namespace App\Services;

use Codedge\Updater\UpdaterManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * @phpstan-type UpdateResult array{
 *   success: bool,
 *   newVersion?: string|null,
 *   message?: string|null,
 *   error?: string|null
 * }
 */
class SelfUpdateService
{
    public function __construct(
        private UpdaterManager $updater,
        private Dispatcher $events,
    ) {}

    /**
     * Check if a newer version than $currentVersion exists.
     */
    public function isUpdateAvailable(string $currentVersion): bool
    {
        return $this->updater->source()->isNewVersionAvailable($currentVersion);
    }

    /**
     * Run the update. Returns a structured array result.
     *
     * @param string $currentVersion
     * @param array{package_file_name?: string|null} $options
     * @return UpdateResult
     */
    public function runUpdate(string $currentVersion, array $options = []): array
    {
        // Optionally override which asset to fetch (helps for "pick a version").
        if (! empty($options['package_file_name'])) {
            Config::set(
                'self-update.repository_types.github.package_file_name',
                $options['package_file_name']
            );
        }

        // Basic preflight: dirs + zip extension present.
        if (! extension_loaded('zip')) {
            return ['success' => false, 'error' => 'The PHP zip extension is not enabled.'];
        }

        try {
            // Maintenance mode with a simple render (optional blade view).
            Artisan::call('down', ['--render' => 'errors::maintenance']);

            // Double-check availability right before updating.
            if (! $this->updater->source()->isNewVersionAvailable($currentVersion)) {
                Artisan::call('up');
                return ['success' => false, 'message' => 'Already up to date.'];
            }

            $ok = $this->updater->update(); // download + extract + mirror
            if (! $ok) {
                Artisan::call('up');
                return ['success' => false, 'error' => 'Updater returned false (mirroring or extraction failed). Check storage/app/self-updater and permissions.'];
            }

            // Post-update: database & caches (adjust to your app’s needs).
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('optimize:clear');
            Artisan::call('view:cache');
            Artisan::call('config:cache');

            // After a successful update:
            $new = $this->detectNewVersion();
            $this->persistInstalledVersion($new);

            Artisan::call('up');

            return [
                'success' => true,
                'newVersion' => $this->detectNewVersion(),
                'message' => 'Application updated successfully.',
            ];
        } catch (Throwable $e) {
            Log::error('SelfUpdate failed', [
                'exception' => $e::class,
                'message' => $e->getMessage(),
            ]);

            // Ensure app is back up even on error.
            try { Artisan::call('up'); } catch (Throwable) {}

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Best-effort way to read the just-installed version (override as needed).
     * - Tag name from RELEASE file, VERSION file, or your own config/Settings.
     */
    private function detectNewVersion(): ?string
    {
        // Prefer a VERSION file you write during packaging:
        $path = base_path('VERSION');
        if (is_file($path)) {
            return trim((string) @file_get_contents($path)) ?: null;
        }

        // Or fall back to env/config if that’s your pattern:
        $v = config('self-update.version_installed');
        return is_string($v) ? $v : null;
    }

    /**
     * Resolve the current installed version.
     * Priority: VERSION file > SystemSettings.installed_version > config/env.
     */
    public function currentVersion(): string
    {
        $fromFile = $this->detectNewVersion();
        if (is_string($fromFile) && $fromFile !== '') {
            return $fromFile;
        }

        if (class_exists(\App\Settings\SystemSettings::class)) {
            /** @var \App\Settings\SystemSettings $settings */
            $settings = app(\App\Settings\SystemSettings::class);
            if (is_string($settings->installed_version) && $settings->installed_version !== '') {
                return $settings->installed_version;
            }
        }

        $v = config('self-update.version_installed');
        return is_string($v) && $v !== '' ? $v : '0.1.0';
    }

    /**
     * Persist the installed version post-update (prefer Settings).
     */
    private function persistInstalledVersion(?string $version): void
    {
        if (! is_string($version) || $version === '') {
            return;
        }

        if (class_exists(\App\Settings\SystemSettings::class)) {
            /** @var \App\Settings\SystemSettings $settings */
            $settings = app(\App\Settings\SystemSettings::class);
            $settings->installed_version = $version;
            $settings->save();
            return;
        }

        // Optional: also mirror to config() so the app reflects it immediately.
        config()->set('self-update.version_installed', $version);
    }
}

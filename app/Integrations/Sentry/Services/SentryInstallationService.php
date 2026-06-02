<?php

namespace App\Integrations\Sentry\Services;

use App\Settings\SentrySettings;

final class SentryInstallationService
{
    public function __construct(private readonly SentrySettings $settings) {}

    /**
     * Handle the 'installation' resource. Action is 'created' or 'deleted'.
     *
     * @param  array<string,mixed>  $payload
     */
    public function handle(array $payload): void
    {
        $action = (string) ($payload['action'] ?? '');
        $uuid = (string) ($payload['installation']['uuid'] ?? '');

        if ($action === 'created' && $uuid !== '') {
            $this->settings->installation_uuid = $uuid;
            $this->settings->save();

            return;
        }

        if ($action === 'deleted') {
            $this->settings->installation_uuid = null;
            $this->settings->save();
        }
    }
}

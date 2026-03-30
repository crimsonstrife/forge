<?php

namespace App\Support\Codex;

use App\Settings\CodexSettings;

final class CodexConnection
{
    public function __construct(private CodexSettings $settings) {}

    public function enabled(): bool
    {
        if ($this->hasSettingsOverrides()) {
            return $this->settings->enabled;
        }

        return (bool) config('codex.enabled');
    }

    public function baseUrl(): string
    {
        $settingsUrl = trim($this->settings->url);

        return rtrim($settingsUrl !== '' ? $settingsUrl : (string) config('codex.url', ''), '/');
    }

    public function token(): string
    {
        $settingsToken = trim((string) ($this->settings->token ?? ''));

        return $settingsToken !== '' ? $settingsToken : (string) config('codex.app_token', '');
    }

    public function configured(): bool
    {
        return $this->enabled() && $this->baseUrl() !== '' && $this->token() !== '';
    }

    private function hasSettingsOverrides(): bool
    {
        return trim($this->settings->url) !== ''
            || trim((string) ($this->settings->token ?? '')) !== '';
    }
}

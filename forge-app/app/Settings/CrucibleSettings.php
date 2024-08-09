<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CrucibleSettings extends Settings
{
    public bool $enabled;
    public string $base_url;
    public string $api_token;

    public static function group(): string
    {
        return 'crucible';
    }

    public static function makeDefault(): self
    {
        return new self([
            'enabled' => false,
            'base_url' => '',
            'api_token' => '',
        ]);
    }

    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->base_url) && !empty($this->api_token);
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->base_url = $baseUrl;
    }

    public function getBaseUrl(): string
    {
        return $this->base_url;
    }

    public static function encrypted(): array
    {
        return ['api_token'];
    }
}

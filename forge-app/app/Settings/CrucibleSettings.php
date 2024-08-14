<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * CrucibleSettings class represents the settings for the Crucible feature.
 *
 * @property bool $enabled Indicates whether the Crucible feature is enabled or not.
 * @property string $base_url The base URL for the Crucible API.
 * @property string $api_token The API token for accessing the Crucible API.
 */
class CrucibleSettings extends Settings
{
    public bool $enabled;
    public string $base_url;
    public string $api_token;

    /**
     * Returns the group name for Crucible settings.
     *
     * @return string The group name for Crucible settings.
     */
    public static function group(): string
    {
        return 'crucible';
    }

    /**
     * Creates a new instance of CrucibleSettings with default values.
     *
     * @return CrucibleSettings The newly created instance.
     */
    public static function makeDefault(): self
    {
        return new self([
            'enabled' => false,
            'base_url' => '',
            'api_token' => '',
        ]);
    }

    /**
     * Check if the Crucible settings are enabled.
     *
     * @return bool Returns true if the Crucible settings are enabled, false otherwise.
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->base_url) && !empty($this->api_token);
    }

    /**
     * Set the enabled status of the Crucible settings.
     *
     * @param bool $enabled The enabled status to set.
     * @return void
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * Set the base URL for the Crucible settings.
     *
     * @param string $baseUrl The base URL to set.
     * @return void
     */
    public function setBaseUrl(string $baseUrl): void
    {
        $this->base_url = $baseUrl;
    }

    /**
     * Retrieves the base URL for the Crucible settings.
     *
     * @return string The base URL.
     */
    public function getBaseUrl(): string
    {
        return $this->base_url;
    }

    /**
     * Returns an array of encrypted settings.
     *
     * @return array The array of encrypted settings.
     */
    public static function encrypted(): array
    {
        return ['api_token'];
    }
}

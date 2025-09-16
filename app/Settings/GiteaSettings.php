<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @phpstan-type GiteaSettingsShape array{
 *   enabled: bool,
 *   base_url: string,
 *   app_name: string,
 *   client_id: string|null,
 *   client_secret: string|null,
 *   webhook_secret: string|null,
 *   personal_access_token: string|null
 * }
 */
final class GiteaSettings extends Settings
{
    public bool $enabled;
    public string $base_url; // e.g. https://gitea.example.com

    public string $app_name;
    public ?string $client_id;

    public ?string $client_secret;

    public ?string $webhook_secret;

    public ?string $personal_access_token;

    public static function group(): string
    {
        return 'gitea';
    }

    public static function encrypted(): array
    {
        return [
            'client_secret',
            'webhook_secret',
            'personal_access_tokens'
        ];
    }
}

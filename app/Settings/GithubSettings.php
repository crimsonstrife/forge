<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @phpstan-type GithubSettingsShape array{
 *   enabled: bool,
 *   app_name: string,
 *   client_id: string,
 *   client_secret: string|null,
 *   webhook_secret: string|null,
 *   personal_access_token: string|null,
 *   api_base: string,
 *   web_base: string
 * }
 */
final class GithubSettings extends Settings
{
    public bool $enabled;
    public string $app_name;
    public string $client_id;

    public ?string $client_secret;

    public ?string $webhook_secret;

    public ?string $personal_access_token;

    public string $api_base;
    public string $web_base;

    public static function group(): string
    {
        return 'github';
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

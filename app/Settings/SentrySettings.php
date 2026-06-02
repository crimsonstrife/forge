<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @phpstan-type SentrySettingsShape array{
 *   enabled: bool,
 *   org_slug: string,
 *   client_id: string,
 *   client_secret: string|null,
 *   auth_token: string|null,
 *   api_base: string,
 *   default_project_id: string|null,
 *   default_issue_type_id: int|null,
 *   default_priority_id: int|null,
 *   installation_uuid: string|null
 * }
 */
final class SentrySettings extends Settings
{
    public bool $enabled;

    public string $org_slug;

    public string $client_id;

    public ?string $client_secret;

    public ?string $auth_token;

    public string $api_base;

    public ?string $default_project_id;

    public ?int $default_issue_type_id;

    public ?int $default_priority_id;

    public ?string $installation_uuid;

    public static function group(): string
    {
        return 'sentry';
    }

    public static function encrypted(): array
    {
        return [
            'client_secret',
            'auth_token',
        ];
    }
}

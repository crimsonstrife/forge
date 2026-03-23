<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @phpstan-type CodexSettingsShape array{
 *   enabled: bool,
 *   url: string,
 *   token: string|null
 * }
 */
final class CodexSettings extends Settings
{
    public bool $enabled;

    /** Base URL of the connected Codex installation (e.g. https://docs.example.com) */
    public string $url;

    /** Machine-to-machine Sanctum PAT issued by the Codex installation */
    public ?string $token;

    public static function group(): string
    {
        return 'codex';
    }

    public static function encrypted(): array
    {
        return ['token'];
    }
}

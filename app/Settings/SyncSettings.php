<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @phpstan-type SyncSettingsShape array{
 *   allow_outbound_issue_updates: bool,
 *   auto_transition_on_pr_merge: bool,
 *   link_keyword_fix: string,
 *   link_keyword_close: string
 * }
 */
final class SyncSettings extends Settings
{
    public bool $allow_outbound_issue_updates;
    public bool $auto_transition_on_pr_merge;

    /** e.g. "Fixes", case-insensitive match in commit messages */
    public string $link_keyword_fix;

    /** e.g. "Closes" */
    public string $link_keyword_close;

    public static function group(): string
    {
        return 'sync';
    }
}

<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

/**
 * @property bool $redact_emails
 * @property bool $redact_phones
 * @property bool $redact_credit_cards
 * @property bool $redact_ssn
 * @property bool $redact_api_keys
 * @property string $public_warning_text
 * @property string $ticket_key_prefix
 */
final class SupportSettings extends Settings
{
    public bool $redact_emails = true;
    public bool $redact_phones = true;
    public bool $redact_credit_cards = true;
    public bool $redact_ssn = true;
    public bool $redact_api_keys = true;

    public string $public_warning_text = "⚠️ Do not include passwords, card numbers, SSNs, or private keys. We attempt automatic redaction, but it's not perfect.";
    public string $ticket_key_prefix = 'SD';

    public static function group(): string
    {
        return 'support';
    }
}

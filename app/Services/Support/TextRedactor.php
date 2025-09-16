<?php

namespace App\Services\Support;

final class TextRedactor
{
    /** @var array<string,bool> */
    public function __construct(private array $flags = [])
    {
    }

    public function redact(string $text): string
    {
        $flags = $this->flags + [
                'emails' => true, 'phones' => true, 'credit_cards' => true, 'ssn' => true, 'api_keys' => true,
            ];

        $t = $text;

        if ($flags['emails']) {
            $t = preg_replace('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', '[email redacted]', $t) ?? $t;
        }
        if ($flags['phones']) {
            $t = preg_replace('/(?:\+?\d{1,3}[\s\-\.]?)?(?:\(?\d{3}\)?[\s\-\.]?)?\d{3}[\s\-\.]?\d{4}/', '[phone redacted]', $t) ?? $t;
        }
        if ($flags['credit_cards']) {
            $t = preg_replace('/\b(?:\d[ -]*?){13,19}\b/', '[card redacted]', $t) ?? $t;
        }
        if ($flags['ssn']) {
            $t = preg_replace('/\b\d{3}-\d{2}-\d{4}\b/', '[ssn redacted]', $t) ?? $t;
        }
        if ($flags['api_keys']) {
            $t = preg_replace('/\b(?:ghp|gho|ghu|ghs|ghr)_[A-Za-z0-9]{36,255}\b/', '[token redacted]', $t) ?? $t;
            $t = preg_replace('/AKIA[0-9A-Z]{16}/', '[aws key redacted]', $t) ?? $t;
            $t = preg_replace('/(?:secret|api[_\- ]?key)\s*[:=\s]\s*[A-Za-z0-9_\-]{8,}/i', '[secret redacted]', $t) ?? $t;
        }

        return $t;
    }
}

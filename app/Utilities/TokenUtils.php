<?php

namespace App\Utilities;

class TokenUtils
{
    public static function isMaskedToken(string $value): bool
    {
        $v = trim($value);
        return $v === '' || preg_match('/^\*+$/', $v) === 1 || str_starts_with($v, '***');
    }
}

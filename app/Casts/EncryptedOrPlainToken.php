<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Throwable;

final class EncryptedOrPlainToken implements CastsAttributes
{
    /**
     * @param array<string,mixed> $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString((string) $value);
        } catch (Throwable $e) {
            return trim((string) $value);
        }
    }

    /**
     * @param array<string,mixed> $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        $v = preg_replace('/\s+/', '', trim((string) $value));

        if ($v === '' || preg_match('/^\*+$/', $v) === 1 || str_starts_with($v, '***')) {
            /** Keep existing stored value when user submits a masked token */
            $original = $model->getRawOriginal($key);
            try {
                // If decryption succeeds, it's already encrypted
                Crypt::decryptString((string) $original);
                return $original;
            } catch (Throwable $e) {
                // If decryption fails, encrypt the original value
                return Crypt::encryptString((string) $original);
            }
        }

        return Crypt::encryptString($v);
    }
}

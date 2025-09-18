<?php

namespace App\Services\Support;

use App\Models\ServiceProduct;
use App\Models\ServiceProductIngestKey;
use Illuminate\Support\Str;
use Random\RandomException;

/**
 * Manages per-ServiceProduct ingest keys.
 *
 * Token format (shown once):  fki_<ulid>.<base64url(32-byte-secret)>
 */
final class IngestKeyManager
{
    /** @return array{token:string, key:ServiceProductIngestKey}
     * @throws RandomException
     */
    public function generate(ServiceProduct $product, ?string $name = null, ?string $createdBy = null): array
    {
        $secret = $this->randomSecret();
        $hash   = $this->hmac($secret);

        $key = ServiceProductIngestKey::create([
            'service_product_id' => $product->getKey(),
            'name' => $name,
            'secret_hash' => $hash,
            'created_by' => $createdBy,
        ]);

        $id = $key->id;

        $token = "fki_" . $id . "." . $this->b64url($secret);

        return ['token' => $token, 'key' => $key];
    }

    public function revoke(ServiceProductIngestKey $key): void
    {
        if (! $key->isRevoked()) {
            $key->revoked_at = now()->toImmutable();
            $key->save();
        }
    }

    public function parseToken(?string $provided): ?array
    {
        if (! is_string($provided) || $provided === '') {
            return null;
        }
        // Accept either "X-Forge-Api-Key: <token>" or "Authorization: Bearer <token>"
        if (str_starts_with($provided, 'Bearer ')) {
            $provided = substr($provided, 7);
        }

        $parts = explode('.', $provided, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$pfxId, $secretB64] = $parts;
        if (! str_starts_with($pfxId, 'fki_')) {
            return null;
        }

        $id     = substr($pfxId, 4);
        $secret = $this->b64urlDecode($secretB64);
        if ($secret === null) {
            return null;
        }

        return ['id' => $id, 'secret' => $secret];
    }

    public function verify(string $secret, string $storedHash): bool
    {
        return hash_equals($storedHash, $this->hmac($secret));
    }

    /**
     * @throws RandomException
     */
    private function randomSecret(): string
    {
        return random_bytes(32);
    }

    private function b64url(string $bin): string
    {
        return rtrim(strtr(base64_encode($bin), '+/', '-_'), '=');
    }

    private function b64urlDecode(string $b64): ?string
    {
        $pad = strlen($b64) % 4;
        if ($pad > 0) {
            $b64 .= str_repeat('=', 4 - $pad);
        }
        $raw = base64_decode(strtr($b64, '-_', '+/'), true);
        return $raw === false ? null : $raw;
    }

    private function hmac(string $secret): string
    {
        $appKey = (string) config('app.key');
        if (str_starts_with($appKey, 'base64:')) {
            $decoded = base64_decode(substr($appKey, 7), true);
            if ($decoded === false) {
                throw new \RuntimeException('Failed to decode base64 app key. Please check your APP_KEY configuration.');
            }
            $appKey = $decoded;
        }
        return hash_hmac('sha256', $secret, $appKey);
    }
}

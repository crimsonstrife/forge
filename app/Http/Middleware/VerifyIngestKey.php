<?php

namespace App\Http\Middleware;

use App\Models\ServiceProductIngestKey;
use App\Services\Support\IngestKeyManager;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyIngestKey
{
    public function __construct(private IngestKeyManager $manager)
    {
    }

    public function __invoke(Request $request, Closure $next): Response
    {
        // Extract token from either header:
        $raw = $request->header('X-Forge-Api-Key') ?: $request->header('Authorization');
        $parsed = $this->manager->parseToken($raw);

        if ($parsed === null) {
            abort(401, 'Invalid ingest key.');
        }

        /** @var ServiceProductIngestKey|null $key */
        $key = ServiceProductIngestKey::query()->find($parsed['id']);
        if ($key === null || $key->isRevoked()) {
            abort(401, 'Invalid ingest key.');
        }

        if (! $this->manager->verify($parsed['secret'], $key->secret_hash)) {
            abort(401, 'Invalid ingest key.');
        }

        // Stamp last_used_at (quiet)
        $key->forceFill(['last_used_at' => now()->toImmutable()])->saveQuietly();

        // Attach for downstream consumers
        $request->attributes->set('ingest_key', $key);

        // if client sent service_product_id and it's not this key's product -> forbid
        $incoming = $request->input('service_product_id');
        if (is_string($incoming) && $incoming !== $key->service_product_id) {
            abort(403, 'Key is not valid for the requested product.');
        }

        return $next($request);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\ServiceProductIngestKey;
use Illuminate\Console\Command;

class IngestKeyRevoke extends Command
{
    protected $signature = 'forge:ingest-keys:revoke {keyId}';
    protected $description = 'Revoke an ingest key';

    public function handle(): int
    {
        $key = ServiceProductIngestKey::query()->findOrFail($this->argument('keyId'));
        if ($key->isRevoked()) {
            $this->warn('Key already revoked.');
            return self::SUCCESS;
        }

        $key->revoked_at = now()->toImmutable();
        $key->save();

        $this->info('Key revoked.');
        return self::SUCCESS;
    }
}

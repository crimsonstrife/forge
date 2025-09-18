<?php

namespace App\Console\Commands;

use App\Models\ServiceProduct;
use App\Services\Support\IngestKeyManager;
use Illuminate\Console\Command;
use Random\RandomException;

class IngestKeyCreate extends Command
{
    protected $signature = 'forge:ingest-keys:create {serviceProductId} {--name=}';
    protected $description = 'Create an ingest key for a Service Product';

    /**
     * @throws RandomException
     */
    public function handle(IngestKeyManager $mgr): int
    {
        $sp = ServiceProduct::query()->findOrFail($this->argument('serviceProductId'));
        $res = $mgr->generate($sp, $this->option('name'), null);

        $this->info('Key created. Copy this token now:');
        $this->line($res['token']);

        return self::SUCCESS;
    }
}

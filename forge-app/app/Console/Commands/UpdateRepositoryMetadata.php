<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectRepository;
use App\Services\CrucibleService;
use Illuminate\Support\Facades\Log;

class UpdateRepositoryMetadata extends Command
{
    protected $signature = 'repositories:update-metadata';
    protected $description = 'Update metadata for all repositories';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $crucibleService = app(CrucibleService::class);

        if (!$crucibleService->isEnabled()) {
            Log::warning('Crucible connection is disabled or not configured properly.');
            return;
        }

        $repositories = ProjectRepository::all();

        foreach ($repositories as $repository) {
            $repository->updateMetadata();
        }

        $this->info('Repository metadata updated successfully.');
    }
}

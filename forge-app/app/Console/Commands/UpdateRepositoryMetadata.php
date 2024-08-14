<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectRepository;
use App\Services\CrucibleService;
use Illuminate\Support\Facades\Log;

/**
 * Class UpdateRepositoryMetadata
 *
 * This class represents a command to update metadata for all repositories.
 */
class UpdateRepositoryMetadata extends Command
{
    protected $signature = 'repositories:update-metadata';
    protected $description = 'Update metadata for all repositories';

    /**
     * UpdateRepositoryMetadata constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Updates the metadata for all project repositories.
     *
     * This method checks if the Crucible connection is enabled and properly configured. If not, it logs a warning and returns.
     * It retrieves all project repositories and iterates over each repository to update its metadata.
     * Finally, it displays a success message indicating that the repository metadata has been updated successfully.
     *
     * @return void
     */
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

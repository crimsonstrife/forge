<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProjectRepository;

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
        $repositories = ProjectRepository::all();

        foreach ($repositories as $repository) {
            $repository->updateMetadata();
        }

        $this->info('Repository metadata updated successfully.');
    }
}

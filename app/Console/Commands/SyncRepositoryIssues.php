<?php

namespace App\Console\Commands;

use App\Jobs\InitialImportRepositoryIssues;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\Repository;
use Illuminate\Console\Command;
use Throwable;

final class SyncRepositoryIssues extends Command
{
    /** @var string */
    protected $signature = 'repo:sync
        {--all : Sync all projects that have a linked external repository}
        {--link= : ProjectRepository (link) UUID}
        {--project= : Project ID or key/slug}
        {--provider=github : Provider slug (github|gitlab|gitea)}
        {--owner= : Repository owner/org}
        {--name= : Repository name}
        {--queue : Dispatch to queue instead of running inline}';

    /** @var string */
    protected $description = 'Import/sync issues from connected repositories into one project or all projects.';

    public function handle(): int
    {
        if ($this->option('all')) {
            return $this->handleAll();
        }

        $link = $this->resolveLink();

        if (! $link) {
            $this->error('Could not resolve a project-repository link. Provide --link OR all of --project --provider --owner --name, or use --all.');

            return self::INVALID;
        }

        $ok = $this->syncLink($link);

        return $ok ? self::SUCCESS : self::FAILURE;
    }

    /**
     * Sync all linked repositories across all projects.
     */
    private function handleAll(): int
    {
        $with = ['project', 'repository', 'repository.statusMappings'];

        $total = ProjectRepository::query()
            ->whereHas('repository')
            ->count();

        if ($total === 0) {
            $this->warn('No project-repository links found.');

            return self::SUCCESS;
        }

        $this->info("Syncing all linked repositories ({$total})…");
        $this->newLine();

        $failures = 0;

        // Stream results to keep memory usage low.
        foreach (ProjectRepository::query()->with($with)->whereHas('repository')->cursor() as $link) {
            /** @var ProjectRepository $link */
            try {
                $ok = $this->syncLink($link);
            } catch (Throwable $e) {
                $failures++;
                $this->error("Failed to sync link (ID: {$link->id}): ".$e->getMessage());

                continue;
            }
            if (! $ok) {
                $failures++;
            }
            $this->newLine();
        }

        if ($this->option('queue')) {
            $this->info("Dispatched {$total} job(s) to the queue.");

            return self::SUCCESS;
        }

        if ($failures > 0) {
            $this->error("Completed with {$failures} failure(s).");

            return self::FAILURE;
        }

        $this->info('All syncs completed successfully.');

        return self::SUCCESS;
    }

    /**
     * Perform a sync for a single link, queued or inline.
     */
    private function syncLink(ProjectRepository $link): bool
    {
        $this->line(sprintf(
            '<info>Syncing</info> %s/%s (%s) <comment>→</comment> Project %s',
            $link->repository->owner,
            $link->repository->name,
            $link->repository->provider,
            $link->project->name ?? $link->project->id
        ));

        if ($this->option('queue')) {
            InitialImportRepositoryIssues::dispatch($link->id);
            $this->info('Dispatched to queue.');

            return true;
        }

        $this->warn('Running sync inline (no queue)…');
        \Bus::dispatchSync(new InitialImportRepositoryIssues($link->id));

        $link->refresh();

        $this->line('Started:  '.($link->initial_import_started_at?->toDateTimeString() ?? '—'));
        $this->line('Finished: '.($link->initial_import_finished_at?->toDateTimeString() ?? '—'));
        $this->line('Status:   '.($link->last_sync_status ?? '—'));

        if ($link->last_sync_status === 'error') {
            $this->newLine();
            $this->error('Error: '.($link->last_sync_error ?: 'The sync completed with an error status.'));

            return false;
        }

        if ($link->last_sync_error) {
            $this->newLine();
            $this->warn('Note: '.$link->last_sync_error);
        }

        $this->info('Sync complete.');

        return true;
    }

    /**
     * Resolve a single ProjectRepository link by:
     * 1) --link (UUID), or
     * 2) --project + --provider + --owner + --name
     */
    private function resolveLink(): ?ProjectRepository
    {
        $with = ['project', 'repository', 'repository.statusMappings'];

        if ($id = $this->option('link')) {
            return ProjectRepository::query()->with($with)->find($id);
        }

        $projectIdOrKey = (string) $this->option('project');
        $provider = strtolower((string) $this->option('provider'));
        $owner = strtolower((string) $this->option('owner'));
        $name = (string) $this->option('name');

        if (! $projectIdOrKey || ! $provider || ! $owner || ! $name) {
            return null;
        }

        $project = Project::query()
            ->where('id', $projectIdOrKey)
            ->orWhere('key', $projectIdOrKey)
            ->orWhere('slug', $projectIdOrKey)
            ->first();

        if (! $project) {
            $this->error('Project not found: '.$projectIdOrKey);

            return null;
        }

        $repo = Repository::query()
            ->where([
                'provider' => $provider,
                'owner' => $owner,
                'name' => $name,
            ])->first();

        if (! $repo) {
            $this->error('Repository not found: '."{$provider}:{$owner}/{$name}");

            return null;
        }

        $link = ProjectRepository::query()
            ->with($with)
            ->where('project_id', $project->id)
            ->where('repository_id', $repo->id)
            ->first();

        if (! $link) {
            $this->error('No link exists for that project/repository pair.');

            return null;
        }

        return $link;
    }
}

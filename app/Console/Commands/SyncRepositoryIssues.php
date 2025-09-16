<?php

namespace App\Console\Commands;

use App\Jobs\InitialImportRepositoryIssues;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\Repository;
use Illuminate\Console\Command;

/**
 * Trigger an initial import/sync of issues from an external repository into a project.
 *
 * Usage examples:
 *  php artisan repo:sync --link=2c4b1e0a-...-d5f9
 *  php artisan repo:sync --project=b46445... --provider=github --owner=helicalgames --name=One-Mans-Poison
 *  php artisan repo:sync --project=FORGE --provider=github --owner=helicalgames --name=forge --queue
 */
final class SyncRepositoryIssues extends Command
{
    /** @var string */
    protected $signature = 'repo:sync
        {--link= : ProjectRepository (link) UUID}
        {--project= : Project ID or key/slug}
        {--provider=github : Provider slug (github|gitlab|gitea)}
        {--owner= : Repository owner/org}
        {--name= : Repository name}
        {--queue : Dispatch to queue instead of running inline}';

    /** @var string */
    protected $description = 'Import/sync issues from a connected repository into a project.';

    public function handle(): int
    {
        $link = $this->resolveLink();

        if (!$link) {
            $this->error('Could not resolve a project-repository link. Provide --link OR all of --project --provider --owner --name.');
            return self::INVALID;
        }

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
            return self::SUCCESS;
        }

        $this->warn('Running sync inline (no queue)…');
        \Bus::dispatchSync(new InitialImportRepositoryIssues($link->id));

        // Reload to show outcome
        $link->refresh();

        $this->line('Started:  ' . ($link->initial_import_started_at?->toDateTimeString() ?? '—'));
        $this->line('Finished: ' . ($link->initial_import_finished_at?->toDateTimeString() ?? '—'));
        $this->line('Status:   ' . ($link->last_sync_status ?? '—'));

        if ($link->last_sync_error) {
            $this->newLine();
            $this->error('Error: ' . $link->last_sync_error);
            return self::FAILURE;
        }

        $this->info('Sync complete.');
        return self::SUCCESS;
    }

    /**
     * Resolve the ProjectRepository link by:
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
        $provider       = strtolower((string) $this->option('provider'));
        $owner          = strtolower((string) $this->option('owner'));
        $name           = (string) $this->option('name');

        if (!$projectIdOrKey || !$provider || !$owner || !$name) {
            return null;
        }

        $project = Project::query()
            ->where('id', $projectIdOrKey)
            ->orWhere('key', $projectIdOrKey)
            ->orWhere('slug', $projectIdOrKey)
            ->first();

        if (!$project) {
            $this->error('Project not found: ' . $projectIdOrKey);
            return null;
        }

        $repo = Repository::query()
            ->where([
                'provider' => $provider,
                'owner'    => $owner,
                'name'     => $name,
            ])->first();

        if (!$repo) {
            $this->error('Repository not found: ' . "{$provider}:{$owner}/{$name}");
            return null;
        }

        $link = ProjectRepository::query()
            ->with($with)
            ->where('project_id', $project->id)
            ->where('repository_id', $repo->id)
            ->first();

        if (!$link) {
            $this->error('No link exists for that project/repository pair.');
            return null;
        }

        return $link;
    }
}

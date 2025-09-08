<?php

namespace App\Livewire\Projects;

use App\Jobs\InitialImportRepositoryIssues;
use App\Models\IssueStatus;
use App\Models\IssueStatusMapping;
use App\Models\Project;
use App\Models\ProjectRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ManageRepository extends Component
{
    use AuthorizesRequests;

    public Project $project;
    public ProjectRepository $link;

    public bool $showEditor = false;

    // Form fields
    public ?string $token = null;
    public ?string $token_type = 'oauth';

    /** @var array<string,string> external_state => issue_status_id */
    public array $statusMapping = [];

    public function mount(Project $project, ProjectRepository $link): void
    {
        $this->authorize('connect', [ProjectRepository::class, $project]);

        $this->project = $project->withoutRelations();
        $this->link = $link->loadMissing(['repository', 'repository.statusMappings']);

        // Prefill mapping from DB
        foreach ($this->link->repository->statusMappings as $m) {
            $this->statusMapping[strtolower($m->external_state)] = (string) $m->issue_status_id;
        }

        // Suggest sensible defaults if not set
        if (!isset($this->statusMapping['open'], $this->statusMapping['closed'])) {
            $statuses = IssueStatus::query()
                ->orderBy('order')
                ->get(['id', 'is_done']);

            $this->statusMapping['open']   = $this->statusMapping['open']   ?? (string) optional($statuses->firstWhere('is_done', false))->id;
            $this->statusMapping['closed'] = $this->statusMapping['closed'] ?? (string) optional($statuses->firstWhere('is_done', true))->id;
        }
    }

    public function openEditor(): void
    {
        $this->showEditor = true;
    }

    public function closeEditor(): void
    {
        $this->reset('token'); // don’t keep it in memory
        $this->showEditor = false;
    }

    #[Validate('nullable|string|min:20')] // rough guard; PATs/OAuth tokens are long
    public function save(): void
    {
        $this->authorize('connect', [ProjectRepository::class, $this->project]);

        // Update token only if provided (blank means keep current)
        if ($this->token !== null && $this->token !== '') {
            $this->link->update([
                'token'      => $this->token,
                'token_type' => $this->token_type ?: 'oauth',
            ]);
        }

        // Persist status mappings for this repository
        foreach (['open', 'closed'] as $k) {
            if (!empty($this->statusMapping[$k])) {
                IssueStatusMapping::query()->updateOrCreate(
                    [
                        'repository_id' => $this->link->repository_id,
                        'provider'      => $this->link->repository->provider,
                        'external_state' => $k,
                    ],
                    ['issue_status_id' => $this->statusMapping[$k]]
                );
            }
        }

        $this->dispatch('notify', body: 'Repository settings updated.');
        $this->closeEditor();

        // Refresh relation for the panel if it depends on latest data
        $this->link->refresh();
        $this->dispatch('repo-link-refreshed');
    }

    public function syncNow(bool $queue = false): void
    {
        $this->authorize('connect', [ProjectRepository::class, $this->project]);

        if ($queue) {
            InitialImportRepositoryIssues::dispatch($this->link->id);
            $this->dispatch('notify', body: 'Sync queued.');
        } else {
            \Bus::dispatchSync(new InitialImportRepositoryIssues($this->link->id));
            $this->dispatch('notify', body: 'Sync completed (inline).');
        }

        $this->link->refresh();
        $this->dispatch('repo-link-refreshed');
    }

    public function render(): View
    {
        $statuses = IssueStatus::query()
            ->orderBy('order')
            ->get(['id','name','is_done'])
            ->map(fn ($s) => ['id' => (string)$s->id, 'name' => $s->name, 'is_done' => (bool)$s->is_done])
            ->all();

        return view('livewire.projects.manage-repository', compact('statuses'));
    }
}

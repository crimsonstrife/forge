<?php
namespace App\Livewire\Projects;

use App\Jobs\InitialImportRepositoryIssues;
use App\Models\IssueStatus;
use App\Models\IssueStatusMapping;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\Repository;
use App\Services\CrucibleService;
use Bus;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

final class ConnectRepository extends Component
{
    use AuthorizesRequests;

    public Project $project;

    public bool $projectHasIssues = false;

    public string $provider = 'github';
    public string $host = 'github.com';
    public string $owner = '';
    public string $name  = '';

    // token the integrator just granted (store encrypted)
    public ?string $token = null;

    /** @var array<string,string> */
    public array $statusMapping = []; // e.g. ['open' => '{id-of-status}', 'closed' => '{id}']

    public string $crucibleSearch = '';

    /** @var array<int, array<string, mixed>> */
    public array $crucibleRepositories = [];

    /** @var array<string, mixed>|null */
    public ?array $selectedCrucibleRepository = null;

    public bool $loadingCrucibleRepositories = false;

    public ?string $crucibleError = null;

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);
        $this->project = $project;
        $this->projectHasIssues = $project->issues()->exists();
    }

    public function save(): void
    {
        $this->authorize('update', $this->project);

        if ($this->project->repositoryLink()->exists()) {
            throw ValidationException::withMessages([
                'provider' => 'This project already has a linked repository. Disconnect it first to change providers.',
            ]);
        }

        if ($this->provider === 'crucible') {
            $this->saveCrucibleRepositoryLink();

            return;
        }

        $this->validate([
            'provider' => 'required|in:github,crucible',
            'host' => 'required|string',
            'owner' => 'required|string',
            'name' => 'required|string',
            'token' => 'nullable|string|min:20',
        ]);

        if ($this->projectHasIssues) {
            throw ValidationException::withMessages([
                'provider' => 'GitHub issue import is only available on projects without issues. Link a Crucible repository instead, or use a new project for import.',
            ]);
        }

        // normalize (GitHub is case-insensitive; store canonical)
        $owner = strtolower(trim($this->owner));
        $name  = trim($this->name);
        $host  = strtolower(trim($this->host));

        $repo = Repository::query()->firstOrCreate([
            'provider' => $this->provider,
            'host'     => $host,
            'owner'    => $owner,
            'name'     => $name,
        ]);

        $link = ProjectRepository::query()->create([
            'project_id'         => $this->project->id,
            'repository_id'      => $repo->id,
            'integrator_user_id' => auth()->id(),
            'token'              => $this->token,
            'token_type'         => $this->token ? 'oauth' : null,
        ]);

        foreach ($this->statusMapping as $external => $statusId) {
            IssueStatusMapping::query()->updateOrCreate(
                ['repository_id' => $repo->id, 'provider' => $this->provider, 'external_state' => $external],
                ['issue_status_id' => $statusId]
            );
        }

        Bus::dispatchSync(new InitialImportRepositoryIssues($link->id));

        $this->dispatch('notify', body: 'Repository connected. Initial import started.');
        $this->redirectRoute('projects.code', ['project' => $this->project]);
    }

    public function updatedProvider(string $provider): void
    {
        $this->reset('crucibleError', 'crucibleRepositories', 'selectedCrucibleRepository', 'crucibleSearch');
        $this->owner = '';
        $this->name = '';

        if ($provider === 'crucible') {
            $this->token = null;
            $this->host = app(CrucibleService::class)->host();

            return;
        }

        $this->host = 'github.com';
    }

    public function loadCrucibleRepositories(): void
    {
        $this->authorize('update', $this->project);

        $this->loadingCrucibleRepositories = true;
        $this->crucibleError = null;
        $this->crucibleRepositories = [];

        $service = app(CrucibleService::class);

        if (! $service->isConfigured()) {
            $this->crucibleError = 'Crucible integration is not configured. Set CRUCIBLE_ENABLED, CRUCIBLE_URL, and CRUCIBLE_APP_TOKEN.';
            $this->loadingCrucibleRepositories = false;

            return;
        }

        try {
            $this->crucibleRepositories = $service->searchRepositories($this->crucibleSearch, 20, (string) auth()->id());
        } catch (\Throwable $e) {
            $this->crucibleError = 'Could not load Crucible repositories: ' . $e->getMessage();
        }

        $this->loadingCrucibleRepositories = false;
    }

    public function selectCrucibleRepository(string $organizationSlug, string $repositorySlug): void
    {
        $this->authorize('update', $this->project);

        try {
            $selected = collect($this->crucibleRepositories)->first(
                fn (array $repo) => $repo['organization_slug'] === $organizationSlug && $repo['slug'] === $repositorySlug
            );

            if (! $selected) {
                $selected = app(CrucibleService::class)->getRepository($organizationSlug, $repositorySlug, (string) auth()->id());
            }
        } catch (\Throwable $e) {
            $this->crucibleError = 'Could not load that Crucible repository: ' . $e->getMessage();

            return;
        }

        if (! $selected) {
            $this->crucibleError = 'That Crucible repository could not be loaded.';

            return;
        }

        $this->selectedCrucibleRepository = $selected;
        $this->owner = (string) $selected['organization_slug'];
        $this->name = (string) $selected['slug'];
        $this->host = app(CrucibleService::class)->host();
        $this->crucibleError = null;
    }

    public function clearCrucibleSelection(): void
    {
        $this->authorize('update', $this->project);

        $this->selectedCrucibleRepository = null;
        $this->owner = '';
        $this->name = '';
    }

    private function saveCrucibleRepositoryLink(): void
    {
        $this->validate([
            'provider' => 'required|in:github,crucible',
            'owner' => 'required|string',
            'name' => 'required|string',
        ]);

        $service = app(CrucibleService::class);

        if (! $service->isConfigured()) {
            throw ValidationException::withMessages([
                'provider' => 'Crucible integration is not configured. Set CRUCIBLE_ENABLED, CRUCIBLE_URL, and CRUCIBLE_APP_TOKEN.',
            ]);
        }

        try {
            $remote = $service->getRepository(trim($this->owner), trim($this->name), (string) auth()->id());
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'provider' => 'Could not verify the Crucible repository: ' . $e->getMessage(),
            ]);
        }

        if (! $remote) {
            throw ValidationException::withMessages([
                'name' => 'That Crucible repository could not be found.',
            ]);
        }

        // Register the Forge integration on Crucible's side if not already linked.
        $existingProjectId = $remote['forge_project_id'] ?? '';
        if ($existingProjectId !== (string) $this->project->id) {
            try {
                $service->registerForgeIntegration(
                    trim($this->owner),
                    trim($this->name),
                    (string) $this->project->id,
                    $this->project->name,
                    (string) auth()->id(),
                );
            } catch (\Throwable $e) {
                throw ValidationException::withMessages([
                    'provider' => 'Could not register the integration on Crucible: ' . $e->getMessage(),
                ]);
            }
        }

        $repo = Repository::query()->updateOrCreate(
            [
                'provider' => 'crucible',
                'host' => $service->host(),
                'owner' => (string) $remote['organization_slug'],
                'name' => (string) $remote['slug'],
            ],
            [
                'external_id' => $remote['id'] ?: null,
                'default_branch' => $remote['default_branch'] ?: null,
                'meta' => [
                    'organization_name' => $remote['organization_name'] ?? $remote['organization_slug'],
                    'repository_name' => $remote['name'] ?? $remote['slug'],
                    'repository_slug' => $remote['slug'],
                    'web_url' => $remote['web_url'] ?? null,
                    'forge_project_id' => (string) $this->project->id,
                    'forge_project_name' => $this->project->name,
                    'forge_url' => config('app.url'),
                    'visibility' => $remote['visibility'] ?? null,
                    'vcs_type' => $remote['vcs_type'] ?? null,
                ],
            ]
        );

        ProjectRepository::query()->create([
            'project_id' => $this->project->id,
            'repository_id' => $repo->id,
            'integrator_user_id' => auth()->id(),
        ]);

        $this->dispatch('notify', body: 'Crucible repository linked.');
        $this->redirectRoute('projects.code', ['project' => $this->project]);
    }

    public function render(): View
    {
        /** @var array<int,array{id:string,name:string,is_done:bool}> $statuses */
        $statuses = IssueStatus::query()
            ->orderBy('order')->get(['id','name','is_done'])
            ->map(fn($s) => ['id'=>$s->id,'name'=>$s->name,'is_done'=>$s->is_done])->all();

        // suggest defaults if empty
        if ($this->provider !== 'crucible' && empty($this->statusMapping)) {
            $openId   = collect($statuses)->firstWhere('is_done', false)['id'] ?? null;
            $closedId = collect($statuses)->firstWhere('is_done', true)['id'] ?? null;
            $this->statusMapping = array_filter(['open' => $openId, 'closed' => $closedId]);
        }

        return view('livewire.projects.connect-repository', compact('statuses'));
    }
}

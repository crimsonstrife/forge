<?php
namespace App\Livewire\Projects;

use App\Jobs\InitialImportRepositoryIssues;
use App\Models\IssueStatus;
use App\Models\IssueStatusMapping;
use App\Models\Project;
use App\Models\ProjectRepository;
use App\Models\Repository;
use Bus;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ConnectRepository extends Component
{
    use AuthorizesRequests;

    public Project $project;

    #[Validate(['provider' => 'required|in:github,gitlab,gitea'])]
    public string $provider = 'github';

    #[Validate('required|string')]
    public string $host = 'github.com';

    #[Validate('required|string')]
    public string $owner = '';

    #[Validate('required|string')]
    public string $name  = '';

    // token the integrator just granted (store encrypted)
    public ?string $token = null;

    /** @var array<string,string> */
    public array $statusMapping = []; // e.g. ['open' => '{id-of-status}', 'closed' => '{id}']

    public function mount(Project $project): void
    {
        $this->authorize('connect', [ProjectRepository::class, $project]);
        $this->project = $project;
    }

    public function save(): void
    {
        $this->validate();

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
        $this->redirectRoute('projects.show', ['project' => $this->project]);
    }

    public function render(): View
    {
        /** @var array<int,array{id:string,name:string,is_done:bool}> $statuses */
        $statuses = IssueStatus::query()
            ->orderBy('order')->get(['id','name','is_done'])
            ->map(fn($s) => ['id'=>$s->id,'name'=>$s->name,'is_done'=>$s->is_done])->all();

        // suggest defaults if empty
        if (empty($this->statusMapping)) {
            $openId   = collect($statuses)->firstWhere('is_done', false)['id'] ?? null;
            $closedId = collect($statuses)->firstWhere('is_done', true)['id'] ?? null;
            $this->statusMapping = array_filter(['open' => $openId, 'closed' => $closedId]);
        }

        return view('livewire.projects.connect-repository', compact('statuses'));
    }
}

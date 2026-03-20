<?php

namespace App\Livewire\Projects;

use App\Enums\ProjectStage;
use App\Livewire\Projects\Concerns\DeterminesOrganizationScopeAccess;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use App\Services\Projects\ProjectSchemeCloner;
use App\Support\Keys\ProjectKeyGenerator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;
use Throwable;

final class CreateProjectForm extends Component
{
    use AuthorizesRequests;
    use DeterminesOrganizationScopeAccess;

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate(['required', 'string', 'alpha_num:ascii', 'min:2', 'max:10', 'unique:projects,key'])]
    public string $key = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('nullable|uuid|exists:organizations,id')]
    public ?string $organization_id = null;

    #[Validate('nullable|uuid|exists:users,id')]
    public ?string $lead_id = null;

    #[Validate]
    public string $stage = ProjectStage::Planning->value;

    #[Validate('nullable|uuid|exists:projects,id')]
    public ?string $copy_from_project_id = null;

    /** @var array<int, array{id:string,name:string}> */
    public array $teamMembers = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $teamOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $organizationOptions = [];

    /** @var array<int, string> */
    public array $attach_team_ids = []; // uuids as strings

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'key' => ['required', 'string', 'alpha_num:ascii', 'min:2', 'max:10', Rule::unique('projects', 'key')],
            'description' => 'nullable|string|max:10000',
            'organization_id' => 'nullable|uuid|exists:organizations,id',
            'lead_id' => 'nullable|uuid|exists:users,id',
            'stage' => ['required', Rule::in(array_column(ProjectStage::cases(), 'value'))],
            'copy_from_project_id' => 'nullable|uuid|exists:projects,id',
            'attach_team_ids' => ['array'],
            'attach_team_ids.*' => ['uuid', 'exists:teams,id'],
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', Project::class);

        $this->organizationOptions = $this->availableOrganizations()
            ->map(fn (Organization $organization) => ['id' => (string) $organization->id, 'name' => $organization->name])
            ->values()
            ->all();

        $this->teamOptions = $this->availableTeams()
            ->map(fn (Team $team) => ['id' => (string) $team->id, 'name' => $team->name])
            ->values()
            ->all();

        if (auth()->user()?->currentTeam) {
            $this->attach_team_ids = [(string) auth()->user()->currentTeam->id];
        }

        $this->refreshAssignableUsers();
    }

    public function updatedAttachTeamIds(): void
    {
        $this->refreshAssignableUsers();

        if ($this->lead_id && ! collect($this->teamMembers)->pluck('id')->contains($this->lead_id)) {
            $this->lead_id = null;
        }
    }

    public function updatedName(string $value): void
    {
        if ($this->key === '') {
            $this->key = app(ProjectKeyGenerator::class)->suggest($value, 3);
        }
    }

    /**
     * @throws Throwable
     */
    public function save(ProjectSchemeCloner $cloner): Redirector
    {
        $this->authorize('create', Project::class);
        $data = $this->validate();
        $this->ensureAllowedScopeSelections();

        $project = new Project;
        $project->name = $data['name'];
        $project->key = Str::upper($data['key']);
        $project->description = $data['description'] ?? null;
        $project->organization_id = $data['organization_id'] ?? null;
        $project->lead_id = $data['lead_id'] ?? auth()->id();
        $project->stage = ProjectStage::from($data['stage']);
        $project->settings = $project->settings ?? $data['settings'] ?? [];
        $project->save();

        if (! empty($this->attach_team_ids)) {
            $project->teams()->sync($this->teamSyncPayload());
        }

        $project->users()->syncWithoutDetaching([auth()->id() => ['role' => 'Owner']]);

        $project->save();

        // Optionally clone schemes from another project; otherwise ProjectObserver seeds defaults.
        if ($this->copy_from_project_id) {
            $cloner->cloneSchemes($this->copy_from_project_id, $project->id);
        }

        session()->flash('flash.banner', 'Project created.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('projects.show', ['project' => $project]);
    }

    public function render(): mixed
    {
        return view('livewire.projects.create-project-form', [
            'stages' => ProjectStage::cases(),
            'projectsForCopy' => Project::query()
                ->visibleTo(auth()->user())
                ->orderBy('name')->get(['id', 'name']),
        ]);
    }

    private function availableOrganizations()
    {
        $user = auth()->user();

        $query = Organization::query()->orderBy('name');

        if (! $this->canManageAllOrganizations()) {
            $query->visibleTo($user);
        }

        return $query->get(['id', 'name']);
    }

    private function availableTeams()
    {
        $user = auth()->user();

        if ($this->canManageAllOrganizations()) {
            return Team::query()
                ->orderBy('name')
                ->get(['id', 'name', 'user_id']);
        }

        return $user?->allTeams()
            ->sortBy('name')
            ->values() ?? collect();
    }

    private function refreshAssignableUsers(): void
    {
        $teamIds = array_values(array_filter($this->attach_team_ids));

        if ($teamIds === []) {
            $this->teamMembers = [[
                'id' => (string) auth()->id(),
                'name' => (string) auth()->user()?->name,
            ]];

            return;
        }

        $users = Team::query()
            ->whereKey($teamIds)
            ->with(['users:id,name', 'owner:id,name'])
            ->get()
            ->flatMap(function (Team $team) {
                return $team->users
                    ->push($team->owner)
                    ->filter();
            })
            ->unique('id')
            ->sortBy('name')
            ->values();

        $this->teamMembers = $users
            ->map(fn (User $user) => ['id' => (string) $user->id, 'name' => $user->name])
            ->all();
    }

    private function ensureAllowedScopeSelections(): void
    {
        $selectedTeamIds = collect($this->attach_team_ids)
            ->filter()
            ->unique()
            ->values();
        $allowedTeamIds = collect($this->teamOptions)->pluck('id');

        abort_unless(
            $allowedTeamIds->intersect($selectedTeamIds)->count() === $selectedTeamIds->count(),
            403
        );

        if ($this->organization_id === null) {
            return;
        }

        $allowedOrganizationIds = collect($this->organizationOptions)->pluck('id');

        abort_unless($allowedOrganizationIds->contains($this->organization_id), 403);
    }

    /** @return array<string, array{role:string}> */
    private function teamSyncPayload(): array
    {
        return collect($this->attach_team_ids)
            ->filter()
            ->unique()
            ->mapWithKeys(fn (string $teamId) => [$teamId => ['role' => 'Contributor']])
            ->all();
    }
}

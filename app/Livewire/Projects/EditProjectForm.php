<?php

namespace App\Livewire\Projects;

use App\Enums\ProjectStage;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class EditProjectForm extends Component
{
    use AuthorizesRequests;

    public Project $project;

    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate(['required', 'string', 'alpha_num:ascii', 'min:2', 'max:10'])]
    public string $key = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('nullable|uuid|exists:organizations,id')]
    public ?string $organization_id = null;

    #[Validate('nullable|uuid|exists:users,id')]
    public ?string $lead_id = null;

    #[Validate]
    public string $stage = ProjectStage::Planning->value;

    #[Validate('nullable|date')]
    public ?string $started_at = null;

    #[Validate('nullable|date')]
    public ?string $due_at = null;

    #[Validate('nullable|date')]
    public ?string $ended_at = null;

    /** @var array<int, array{id:string,name:string}> */
    public array $teamOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $organizationOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $leadOptions = [];

    /** @var array<int, string> */
    public array $attach_team_ids = [];

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);
        $this->project = $project;
        $this->name = $project->name;
        $this->key = $project->key;
        $this->description = $project->description;
        $this->organization_id = $project->organization_id;
        $this->lead_id = $project->lead_id;
        $this->stage = ($project->stage?->value) ?? ProjectStage::Planning->value;
        $this->started_at = optional($project->started_at)->toDateString();
        $this->due_at = optional($project->due_at)->toDateString();
        $this->ended_at = optional($project->ended_at)->toDateString();
        $this->attach_team_ids = $project->teams()
            ->pluck('teams.id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $this->organizationOptions = $this->availableOrganizations()
            ->map(fn (Organization $organization) => ['id' => (string) $organization->id, 'name' => $organization->name])
            ->values()
            ->all();

        $this->teamOptions = $this->availableTeams()
            ->map(fn (Team $team) => ['id' => (string) $team->id, 'name' => $team->name])
            ->values()
            ->all();

        $this->refreshAssignableUsers();

        if ($this->lead_id && ! collect($this->leadOptions)->pluck('id')->contains($this->lead_id)) {
            $this->leadOptions[] = [
                'id' => (string) $this->project->lead?->id,
                'name' => (string) $this->project->lead?->name,
            ];
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'key' => ['required', 'string', 'alpha_num:ascii', 'min:2', 'max:10', Rule::unique('projects', 'key')->ignore($this->project->id)],
            'description' => 'nullable|string|max:10000',
            'organization_id' => 'nullable|uuid|exists:organizations,id',
            'lead_id' => 'nullable|uuid|exists:users,id',
            'stage' => ['required', Rule::in(array_column(ProjectStage::cases(), 'value'))],
            'started_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
            'attach_team_ids' => ['array'],
            'attach_team_ids.*' => ['uuid', 'exists:teams,id'],
        ];
    }

    public function updatedAttachTeamIds(): void
    {
        $this->refreshAssignableUsers();

        if ($this->lead_id && ! collect($this->leadOptions)->pluck('id')->contains($this->lead_id)) {
            $this->lead_id = null;
        }
    }

    public function save(): mixed
    {
        $this->authorize('update', $this->project);
        $data = $this->validate();
        $this->ensureAllowedScopeSelections();

        $this->project->fill([
            'name' => $data['name'],
            'key' => strtoupper($data['key']),
            'description' => $data['description'] ?? null,
            'organization_id' => $data['organization_id'] ?? null,
            'lead_id' => $data['lead_id'] ?? $this->project->lead_id ?? null,
            'stage' => ProjectStage::from($data['stage']),
            'started_at' => $data['started_at'] ?: null,
            'due_at' => $data['due_at'] ?: null,
            'ended_at' => $data['ended_at'] ?: null,
        ])->save();

        $this->project->teams()->sync($this->teamSyncPayload());

        session()->flash('flash.banner', 'Project updated.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('projects.show', ['project' => $this->project]);
    }

    public function render(): mixed
    {
        return view('livewire.projects.edit-project-form', [
            'stages' => ProjectStage::cases(),
        ]);
    }

    private function availableOrganizations()
    {
        $query = Organization::query()
            ->orderBy('name');

        if (! $this->canManageAllOrganizations()) {
            $query->where(function ($organizations) {
                $organizations
                    ->visibleTo(auth()->user())
                    ->orWhereKey($this->project->organization_id);
            });
        }

        return $query->get(['id', 'name']);
    }

    private function availableTeams()
    {
        if ($this->canManageAllOrganizations()) {
            return Team::query()
                ->orderBy('name')
                ->get(['id', 'name', 'user_id']);
        }

        return Team::query()
            ->where(function ($teams) {
                $teams
                    ->whereIn('id', auth()->user()?->allTeams()->pluck('id') ?? [])
                    ->orWhereHas('projects', fn ($projects) => $projects->whereKey($this->project->getKey()));
            })
            ->orderBy('name')
            ->get(['id', 'name', 'user_id']);
    }

    private function canManageAllOrganizations(): bool
    {
        return auth()->user()?->hasPermissionTo('is-super-admin')
            || auth()->user()?->can('is-admin');
    }

    private function refreshAssignableUsers(): void
    {
        $teamIds = array_values(array_filter($this->attach_team_ids));

        if ($teamIds === []) {
            $users = collect([$this->project->lead, auth()->user()])
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values();

            $this->leadOptions = $users
                ->map(fn (User $user) => ['id' => (string) $user->id, 'name' => $user->name])
                ->all();

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

        $this->leadOptions = $users
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
        $existingRoles = $this->project->teams()
            ->pluck('project_team.role', 'teams.id')
            ->mapWithKeys(fn ($role, $teamId) => [(string) $teamId => (string) ($role ?: 'Contributor')]);

        return collect($this->attach_team_ids)
            ->filter()
            ->unique()
            ->mapWithKeys(function (string $teamId) use ($existingRoles) {
                return [$teamId => ['role' => $existingRoles->get($teamId, 'Contributor')]];
            })
            ->all();
    }
}

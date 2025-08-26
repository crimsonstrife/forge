<?php

namespace App\Livewire\Projects;

use App\Enums\ProjectStage;
use App\Models\Project;
use App\Models\User;
use App\Policies\ProjectPolicy;
use App\Services\Projects\ProjectSchemeCloner;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Support\Str;

final class CreateProjectForm extends Component
{
    #[Validate('required|string|max:120')]
    public string $name = '';

    #[Validate(['required', 'string', 'alpha_num:ascii', 'min:2', 'max:10', 'unique:projects,key'])]
    public string $key = '';

    #[Validate('nullable|string|max:10000')]
    public ?string $description = null;

    #[Validate('nullable|uuid|exists:users,id')]
    public ?string $lead_id = null;

    #[Validate]
    public string $stage = ProjectStage::Planning->value;

    #[Validate('nullable|uuid|exists:projects,id')]
    public ?string $copy_from_project_id = null;

    /** @var array<int, array{id:string,name:string}> */
    public array $teamMembers = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $attach_team_ids = []; // uuids as strings

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'key' => ['required', 'string', 'alpha_num:ascii', 'min:2', 'max:10', Rule::unique('projects', 'key')],
            'description' => 'nullable|string|max:10000',
            'lead_id' => 'nullable|uuid|exists:users,id',
            'stage' => ['required', Rule::in(array_column(ProjectStage::cases(), 'value'))],
            'copy_from_project_id' => 'nullable|uuid|exists:projects,id',
        ];
    }

    public function mount(): void
    {
        $this->authorize('create', Project::class);

        $teams = auth()->user()?->allTeams() ?? collect();
        $this->teamOptions = $teams->map(fn($t)=>['id'=>(string)$t->id,'name'=>$t->name])->values()->all();

        if (auth()->user()?->currentTeam) {
            $this->attach_team_ids = [(string) auth()->user()->currentTeam->id];
        }
    }

    public function updatedName(string $value): void
    {
        if ($this->key === '' && strlen($value) >= 2) {
            $this->key = Str::upper(Str::of($value)->replaceMatches('/[^A-Za-z0-9]/', '')->substr(0, 6)->toString());
        }
    }

    public function save(ProjectSchemeCloner $cloner): mixed
    {
        $this->authorize('create', Project::class);
        $data = $this->validate();

        $project = new Project();
        $project->name = $data['name'];
        $project->key = Str::upper($data['key']);
        $project->description = $data['description'] ?? null;
        $project->lead_id = $data['lead_id'] ?? auth()->id();
        $project->stage = ProjectStage::from($data['stage']);
        // attach selected teams (many-to-many)
        if (! empty($this->attach_team_ids)) {
            $project->teams()->syncWithPivotValues($this->attach_team_ids, ['role' => 'Contributor'], false);
        }
        // also add creator as direct member
        $project->users()->syncWithoutDetaching([auth()->id() => ['role' => 'Owner']]);
        $project->save();

        // Optionally clone schemes from another project; otherwise ProjectObserver seeds defaults.
        if ($this->copy_from_project_id) {
            $cloner->cloneSchemes($this->copy_from_project_id, $project->id);
        }

        session()->flash('flash.banner', 'Project created.');
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('projects.show', $project);
    }

    public function render(): mixed
    {
        return view('livewire.projects.create-project-form', [
            'stages' => ProjectStage::cases(),
            'projectsForCopy' => \App\Models\Project::query()
                ->when(auth()->user()?->currentTeam, fn($q, $team) => $q->where('team_id', $team->id))
                ->orderBy('name')->get(['id', 'name']),
        ]);
    }
}


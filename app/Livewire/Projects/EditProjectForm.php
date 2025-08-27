<?php

namespace App\Livewire\Projects;

use App\Enums\ProjectStage;
use App\Models\Project;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class EditProjectForm extends Component
{
    use AuthorizesRequests;

    public Project $project;

    #[Validate('required|string|max:120')] public string $name = '';
    #[Validate(['required','string','alpha_num:ascii','min:2','max:10'])] public string $key = '';
    #[Validate('nullable|string|max:10000')] public ?string $description = null;
    #[Validate('nullable|uuid|exists:users,id')] public ?string $lead_id = null;
    #[Validate] public string $stage = ProjectStage::Planning->value;
    #[Validate('nullable|date')] public ?string $started_at = null;
    #[Validate('nullable|date')] public ?string $due_at = null;
    #[Validate('nullable|date')] public ?string $ended_at = null;

    public function mount(Project $project): void
    {
        $this->authorize('update', $project);
        $this->project      = $project;
        $this->name         = $project->name;
        $this->key          = $project->key;
        $this->description  = $project->description;
        $this->lead_id      = $project->lead_id;
        $this->stage        = ($project->stage?->value) ?? ProjectStage::Planning->value;
        $this->started_at   = optional($project->started_at)->toDateString();
        $this->due_at       = optional($project->due_at)->toDateString();
        $this->ended_at     = optional($project->ended_at)->toDateString();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'key'  => ['required','string','alpha_num:ascii','min:2','max:10', Rule::unique('projects', 'key')->ignore($this->project->id)],
            'description' => 'nullable|string|max:10000',
            'lead_id' => 'nullable|uuid|exists:users,id',
            'stage' => ['required', Rule::in(array_column(ProjectStage::cases(), 'value'))],
            'started_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'ended_at' => 'nullable|date',
        ];
    }

    public function save(): mixed
    {
        $this->authorize('update', $this->project);
        $data = $this->validate();

        $this->project->fill([
            'name' => $data['name'],
            'key' => strtoupper($data['key']),
            'description' => $data['description'] ?? null,
            'lead_id' => $data['lead_id'] ?? $this->project->lead_id,
            'stage' => ProjectStage::from($data['stage']),
            'started_at' => $data['started_at'] ?: null,
            'due_at' => $data['due_at'] ?: null,
            'ended_at' => $data['ended_at'] ?: null,
        ])->save();

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
}

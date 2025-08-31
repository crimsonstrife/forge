<?php

namespace App\Livewire\Goals;

use App\Enums\GoalStatus;
use App\Enums\GoalType;
use App\Enums\KRAutomation;
use App\Enums\KRDirection;
use App\Enums\MetricUnit;
use App\Models\Goal;
use App\Models\GoalKeyResult;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Edit a Goal + its KRs.
 */
final class UpdateGoalForm extends Component
{
    use AuthorizesRequests;

    public Goal $goal;

    #[Validate(['name' => 'required|string|min:3|max:200'])]
    public string $name = '';

    public string $goal_type;
    public string $status;
    public ?string $description = null;
    public ?string $start_date = null;
    public ?string $due_date = null;

    public string $owner_type;
    public ?string $owner_id;

    /** @var array<int, array{id?:string,name:string,unit:string,direction:string,initial_value:float,current_value:float,target_value:float|null,target_min:float|null,target_max:float|null,automation:string,weight:int,_delete?:bool}> */
    public array $keyResults = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $ownerOptions = [];

    public function mount(Goal $goal): void
    {
        $this->authorize('update', $goal);

        $this->goal = $goal;
        $this->name = $goal->name;
        $this->goal_type = $goal->goal_type->value;
        $this->status = $goal->status->value;
        $this->description = $goal->description;
        $this->start_date = optional($goal->start_date)->toDateString();
        $this->due_date = optional($goal->due_date)->toDateString();
        $this->owner_type = $goal->owner_type;
        $this->owner_id = $goal->owner_id;

        $this->keyResults = $goal->keyResults->map(fn (GoalKeyResult $kr) => [
            'id' => $kr->id,
            'name' => $kr->name,
            'unit' => $kr->unit->value,
            'direction' => $kr->direction->value,
            'initial_value' => (float) $kr->initial_value,
            'current_value' => (float) $kr->current_value,
            'target_value' => $kr->target_value,
            'target_min' => $kr->target_min,
            'target_max' => $kr->target_max,
            'automation' => $kr->automation->value,
            'weight' => (int) $kr->weight,
        ])->all();

        $this->refreshOwnerOptions();
    }

    public function addKeyResult(): void
    {
        $this->keyResults[] = [
            'name' => '',
            'unit' => MetricUnit::Percent->value,
            'direction' => KRDirection::IncreaseTo->value,
            'initial_value' => 0,
            'current_value' => 0,
            'target_value' => 100,
            'target_min' => null,
            'target_max' => null,
            'automation' => KRAutomation::Manual->value,
            'weight' => 1,
        ];
    }

    public function markDelete(int $index): void
    {
        $this->keyResults[$index]['_delete'] = true;
    }

    public function updated(string $name, $value): void
    {
        if ($name === 'owner_type') {
            $this->owner_id = null;
            $this->refreshOwnerOptions();
        }
    }

    private function refreshOwnerOptions(): void
    {
        $this->ownerOptions = match ($this->owner_type) {
            Team::class => Team::query()
                ->orderBy('name')
                ->get(['id','name'])
                ->map(fn ($m) => ['id' => (string)$m->id, 'name' => $m->name])
                ->values()->all(),

            Project::class => Project::query()
                ->orderBy('name')
                ->get(['id','name'])
                ->map(fn ($m) => ['id' => (string)$m->id, 'name' => $m->name])
                ->values()->all(),

            Organization::class => Organization::query()
                ->orderBy('name')
                ->get(['id','name'])
                ->map(fn ($m) => ['id' => (string)$m->id, 'name' => $m->name])
                ->values()->all(),

            default => [],
        };
    }


    public function save(): void
    {
        $this->authorize('update', $this->goal);

        $validated = $this->validate([
            'name' => ['required','string','min:3','max:200'],
            'goal_type' => ['required','in:objective,kpi,smart,initiative'],
            'status' => ['required','in:draft,active,paused,completed,canceled'],
            'description' => ['nullable','string','max:5000'],
            'start_date' => ['nullable','date'],
            'due_date' => ['nullable','date','after_or_equal:start_date'],
            'owner_type' => ['required','string'],
            'owner_id' => ['required','uuid'],
            'keyResults' => ['array'],
            'keyResults.*.name' => ['required','string','max:200'],
            'keyResults.*.unit' => ['required','in:number,percent,currency,duration'],
            'keyResults.*.direction' => ['required','in:increase_to,decrease_to,maintain_between,hit_exact'],
            'keyResults.*.initial_value' => ['numeric'],
            'keyResults.*.current_value' => ['numeric'],
            'keyResults.*.target_value' => ['nullable','numeric'],
            'keyResults.*.target_min' => ['nullable','numeric'],
            'keyResults.*.target_max' => ['nullable','numeric'],
            'keyResults.*.automation' => ['required','in:manual,issues_done_percent,story_points_done_percent'],
            'keyResults.*.weight' => ['integer','min:1'],
        ]);

        $this->goal->update([
            'name' => $validated['name'],
            'goal_type' => $validated['goal_type'],
            'status' => $validated['status'],
            'description' => $validated['description'] ?? null,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date) : null,
            'due_date' => $this->due_date ? Carbon::parse($this->due_date) : null,
            'owner_type' => $validated['owner_type'],
            'owner_id' => $validated['owner_id'],
        ]);

        // Sync KRs (create, update, delete)
        $incoming = collect($validated['keyResults']);
        $existing = $this->goal->keyResults()->get()->keyBy('id');

        foreach ($incoming as $row) {
            if (!empty($row['_delete']) && !empty($row['id'])) {
                $this->goal->keyResults()->whereKey($row['id'])->delete();
                continue;
            }

            $payload = [
                'name' => $row['name'],
                'unit' => $row['unit'],
                'direction' => $row['direction'],
                'initial_value' => (float) $row['initial_value'],
                'current_value' => (float) $row['current_value'],
                'target_value' => $row['target_value'],
                'target_min' => $row['target_min'],
                'target_max' => $row['target_max'],
                'automation' => $row['automation'],
                'weight' => (int) $row['weight'],
            ];

            if (!empty($row['id']) && $existing->has($row['id'])) {
                $this->goal->keyResults()->whereKey($row['id'])->update($payload);
            } else {
                $this->goal->keyResults()->create($payload);
            }
        }

        $this->redirectRoute('goals.show', ['goal' => $this->goal], navigate: true);
    }

    public function render(): View
    {
        return view('livewire.goals.update-goal-form', [
            'typeOptions' => collect(GoalType::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all(),
            'statusOptions' => collect(GoalStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])->all(),
            'ownerTypes' => [
                Team::class => 'Team',
                Project::class => 'Project',
                Organization::class => 'Organization',
            ],
        ]);
    }
}

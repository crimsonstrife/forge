<?php
namespace App\Livewire\Goals;

use App\Models\Goal;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

final class ManageGoalDependencies extends Component
{
    use AuthorizesRequests;

    public Goal $goal;
    public string $q = '';
    /** @var array<int, array{id:string,label:string}> */
    public array $results = [];

    public function mount(Goal $goal): void
    {
        $this->authorize('update', $goal);
        $this->goal = $goal->loadMissing(['blockers', 'blocks']);
    }

    public function updatedQ(): void
    {
        $q = trim($this->q);
        if ($q === '') {
            $this->results = [];
            return;
        }

        $this->results = Goal::query()
            ->where('id', '!=', $this->goal->id)
            ->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(8)
            ->get(['id', 'name'])
            ->map(fn($g) => ['id' => (string) $g->id, 'label' => $g->name])
            ->all();
    }

    public function addBlocker(string $goalId): void
    {
        $this->authorize('update', $this->goal);
        $blocker = Goal::query()->findOrFail($goalId);

        if ($this->goal->wouldCreateDependencyCycle($blocker)) {
            $this->dispatch('toast', body: 'Cannot add blocker that causes a dependency cycle.', type: 'danger');
            return;
        }

        $this->goal->blockers()->syncWithoutDetaching([$blocker->id]);
        $this->goal = $this->goal->fresh(['blockers', 'blocks']);
        $this->reset('q', 'results');
        $this->dispatch('toast', body: 'Blocker added.');
    }

    public function removeBlocker(string $goalId): void
    {
        $this->authorize('update', $this->goal);
        $this->goal->blockers()->detach($goalId);
        $this->goal = $this->goal->fresh(['blockers', 'blocks']);
        $this->dispatch('toast', body: 'Blocker removed.');
    }

    public function render(): View
    {
        return view('livewire.goals.manage-goal-dependencies');
    }
}

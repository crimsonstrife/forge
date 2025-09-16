<?php

namespace App\Livewire\Goals;

use App\Models\GoalKeyResult;
use App\Models\GoalCheckin;
use App\Services\GoalProgressService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

final class QuickCheckin extends Component
{
    use AuthorizesRequests;

    public GoalKeyResult $kr;

    public float $value = 0.0;
    public ?string $note = null;

    public function mount(GoalKeyResult $kr): void
    {
        $this->authorize('update', $kr->goal);
        $this->kr = $kr;
        $this->value = (float) ($kr->current_value ?? $kr->initial_value ?? 0);
    }

    public function save(GoalProgressService $svc): void
    {
        $this->authorize('update', $this->kr->goal);

        $this->kr->update(['current_value' => $this->value]);

        GoalCheckin::query()->create([
            'goal_key_result_id' => $this->kr->id,
            'created_by' => auth()->id(),
            'value' => $this->value,
            'note' => $this->note,
        ]);

        // Bump next reminder & recompute goal progress
        $this->kr->goal->bumpNextCheckinAt();
        $svc->recalcGoal($this->kr->goal);

        $this->dispatch('toast', body: 'Check-in saved.');
    }

    public function render(): View
    {
        return view('livewire.goals.quick-checkin');
    }
}

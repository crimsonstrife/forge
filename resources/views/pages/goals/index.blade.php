<?php
use Illuminate\View\View;
use App\Models\Goal;
use App\Enums\GoalType;
use App\Enums\GoalStatus;

use function Laravel\Folio\{name, middleware, render};

name('goals.index');
middleware(['auth','verified']);

render(function (View $view) {
    $q = (string) request('q', '');
    $type = request('type');   // enum backing value
    $status = request('status'); // enum backing value

    $goalsQuery = Goal::query()->with(['owner']);

    if ($q !== '') {
        $goalsQuery->where(function ($qq) use ($q) {
            $qq->where('name', 'like', "%{$q}%")
                ->orWhere('description', 'like', "%{$q}%");
        });
    }
    if ($type) {
        $goalsQuery->where('goal_type', $type);
    }
    if ($status) {
        $goalsQuery->where('status', $status);
    }

    $goals = $goalsQuery->latest()->paginate(20)->withQueryString();

    // Simple option arrays for selects
    $typeOptions = collect(GoalType::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst(str_replace('_', ' ', $c->value))])->all();
    $statusOptions = collect(GoalStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst(str_replace('_', ' ', $c->value))])->all();

    return $view->with(compact('goals', 'q', 'type', 'status', 'typeOptions', 'statusOptions'));
});
?>

<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Goals</h1></x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 1000px">
            <form method="GET" class="row g-2 align-items-end mb-3">
                <div class="col-md-5">
                    <label class="form-label mb-1">Search</label>
                    <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Name or description…">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Any</option>
                        @foreach($typeOptions as $val => $label)
                            <option value="{{ $val }}" @selected($type===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Any</option>
                        @foreach($statusOptions as $val => $label)
                            <option value="{{ $val }}" @selected($status===$val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-flex gap-2">
                    <button class="btn btn-outline-secondary w-100">Filter</button>
                </div>
            </form>

            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('goals.create') }}" class="btn btn-primary">New Goal</a>
            </div>

            @if($goals->count() === 0)
                <div class="card">
                    <div class="card-body text-center text-muted">
                        No goals found. Try adjusting filters or
                        <a href="{{ route('goals.create') }}">create your first goal</a>.
                    </div>
                </div>
            @else
                <div class="list-group">
                    @foreach($goals as $goal)
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                           href="{{ route('goals.show', ['goal' => $goal]) }}">
                    <span class="d-flex align-items-center gap-2">
                        <strong>{{ $goal->name }}</strong>
                        <small class="text-muted">{{ ucfirst($goal->goal_type->value) }}</small>
                        @if($goal->owner)
                            <span class="text-muted small">
                                • Owner: {{ class_basename($goal->owner_type) }}
                                — {{ $goal->owner->name ?? '#' . $goal->owner_id }}
                            </span>
                        @endif
                    </span>
                            <span class="d-inline-flex align-items-center gap-2">
                        <span class="badge bg-secondary">{{ (int) $goal->progress }}%</span>
                    </span>
                        </a>
                    @endforeach
                </div>

                <div class="mt-3">{{ $goals->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>

<?php
use function Laravel\Folio\{name, middleware, render};
use Illuminate\View\View;
use App\Models\Goal;

name('goals.index');
middleware(['auth','verified']);

render(function (View $view) {
    $goals = Goal::query()
        ->with(['owner'])
        ->latest()
        ->paginate(20);

    return $view->with(compact('goals'));
});
?>

<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Goals</h1></x-slot>

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('goals.create') }}" class="btn btn-primary">New Goal</a>
    </div>

    <div class="list-group">
        @foreach($goals as $goal)
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
               href="{{ route('goals.show', ['goal' => $goal]) }}">
                <span>
                    <strong>{{ $goal->name }}</strong>
                    <small class="text-muted ms-2">{{ ucfirst($goal->goal_type->value) }}</small>
                </span>
                <span class="badge bg-secondary">{{ $goal->progress }}%</span>
            </a>
        @endforeach
    </div>

    <div class="mt-3">{{ $goals->links() }}</div>
</x-app-layout>

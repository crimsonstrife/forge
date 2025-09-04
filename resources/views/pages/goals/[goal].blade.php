<?php

use App\Models\Goal;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('goals.show');
middleware(['auth', 'verified']);

/** @param Goal $goal */
render(function (View $view, Goal $goal) {
    $goal->loadMissing([
        'owner',
        'keyResults.checkins',
        'links.linkable',
        'children',
        'parent',
    ]);

    return $view->with(compact('goal'));
});

?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h1 class="h4 mb-0">{{ $goal->name }}</h1>
                <small class="text-muted">
                    {{ ucfirst($goal->goal_type->value) }} • Status: {{ ucfirst($goal->status->value) }}
                    @if($goal->start_date) • {{ $goal->start_date->toFormattedDateString() }}@endif
                    @if($goal->due_date) – {{ $goal->due_date->toFormattedDateString() }}@endif
                    @if($goal->parent) • Parent:
                    <a class="link-primary" href="{{ route('goals.show', $goal->parent) }}">{{ $goal->parent->name }}</a>
                    @endif
                </small>
            </div>
            <div class="ms-3 d-flex gap-2">
                <a href="{{ route('goals.edit', ['goal' => $goal]) }}" class="btn btn-outline-primary">Edit</a>
                <a href="{{ route('goals.index') }}" class="btn btn-outline-secondary">All Goals</a>
            </div>
        </div>
    </x-slot>

    <div class="card mb-4">
        <div class="card-body">
            <div class="mb-2 d-flex align-items-center">
                <div class="me-3 fw-semibold">Progress</div>
                <div class="flex-grow-1">
                    <div class="progress" role="progressbar" aria-valuenow="{{ $goal->progress }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="progress-bar" style="width: {{ $goal->progress }}%">{{ (int) $goal->progress }}%</div>
                    </div>
                </div>
            </div>
            @if($goal->description)
                <p class="mb-0">{{ $goal->description }}</p>
            @else
                <p class="mb-0 text-muted">No description.</p>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">Key Results / KPIs</div>
                <div class="card-body p-0">
                    @if($goal->keyResults->isEmpty())
                        <div class="p-3 text-muted">No key results yet.</div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($goal->keyResults as $kr)
                                @php $percent = number_format($kr->percentComplete(), 2); @endphp
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <div class="fw-semibold">{{ $kr->name }}</div>
                                            <small class="text-muted">
                                                {{ strtoupper($kr->unit->value) }} • {{ str_replace('_',' ', $kr->direction->value) }}
                                                @if(!is_null($kr->target_value)) • Target: {{ $kr->target_value }}@endif
                                                @if(!is_null($kr->target_min) && !is_null($kr->target_max)) • Range: {{ $kr->target_min }}–{{ $kr->target_max }}@endif
                                            </small>
                                        </div>
                                        <div class="text-end" style="min-width:180px">
                                            <div class="small text-muted">Progress</div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" style="width: {{ $percent }}%"></div>
                                            </div>
                                            <div class="small mt-1">{{ $percent }}%</div>
                                        </div>
                                    </div>
                                    @if($kr->checkins->isNotEmpty())
                                        <div class="mt-2">
                                            <small class="text-muted">
                                                Last check-in: {{ optional($kr->checkins->sortByDesc('created_at')->first())->created_at?->diffForHumans() }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if($goal->children->isNotEmpty())
                <div class="card mt-4">
                    <div class="card-header">Sub-goals</div>
                    <ul class="list-group list-group-flush">
                        @foreach($goal->children as $child)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a class="link-primary" href="{{ route('goals.show', $child) }}">{{ $child->name }}</a>
                                <span class="badge bg-secondary">{{ (int) $child->progress }}%</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            @if($goal->owner)
                <div class="card mb-4">
                    <div class="card-header">Owner</div>
                    <div class="card-body">
                        <div class="fw-semibold">{{ class_basename($goal->owner_type) }}</div>
                        <div>{{ $goal->owner->name ?? '—' }}</div>
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">Linked Work</div>
                <div class="card-body p-0">
                    @if($goal->links->isEmpty())
                        <div class="p-3 text-muted">No links yet.</div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($goal->links as $link)
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold">{{ class_basename($link->linkable_type) }}</div>
                                        <small class="text-muted">
                                            @if(method_exists($link->linkable, 'getAttribute'))
                                                {{ $link->linkable->name ?? $link->linkable->summary ?? $link->linkable->id }}
                                            @else
                                                #{{ $link->linkable_id }}
                                            @endif
                                        </small>
                                    </div>
                                    @if(method_exists($link->linkable, 'getRouteKey'))
                                        <a class="btn btn-sm btn-outline-secondary"
                                           href="{{ route(str(class_basename($link->linkable_type))->lower()->plural().'.show', [$link->linkable]) }}">
                                            View
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<?php

use App\Models\{Project, Issue, Organization, Goal};
use Illuminate\Http\Request;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('search');
middleware(['auth','verified']);
/** Provide page data */
render(function (View $view, Request $request) {
    $q = trim((string) $request->query('q', ''));
    $user = auth()->user();

    // Empty state
    if ($q === '') {
        return $view->with([
            'q' => $q,
            'projects' => collect(),
            'issues' => collect(),
            'organizations' => collect(),
            'goals' => collect(),
        ]);
    }

    $like = '%' . str_replace(['%', '_'], ['\%', '\_'], $q) . '%';

    $projects = Project::query()
        ->select(['id', 'name', 'key', 'description'])
        ->where(fn ($sub) => $sub
            ->where('name', 'like', $like)
            ->orWhere('key', 'like', $like)
            ->orWhere('description', 'like', $like))
        ->latest()
        ->limit(10)
        ->get();

    $issues = Issue::query()
        ->select(['id', 'key', 'summary', 'project_id'])
        ->with(['project:id,key'])
        ->where(fn ($sub) => $sub
            ->where('key', 'like', $like)
            ->orWhere('summary', 'like', $like)
            ->orWhere('description', 'like', $like))
        ->latest()
        ->limit(10)
        ->get();

    $organizations = Organization::query()
        ->select(['id', 'name', 'slug'])
        ->where(fn ($sub) => $sub
            ->where('name', 'like', $like))
        ->latest()
        ->limit(10)
        ->get();

    $goals = Goal::query()
        ->select(['id', 'name', 'description'])
        ->where(fn ($sub) => $sub
            ->where('name', 'like', $like)
            ->orWhere('description', 'like', $like))
        ->latest()
        ->limit(10)
        ->get();

    return $view->with(compact('q', 'projects', 'issues', 'organizations', 'goals'));
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <h2 class="h4 mb-0">{{ __('Search Results') }}</h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container d-flex flex-column gap-3">
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3">
                <form method="GET" action="{{ route('search') }}" class="mb-3">
                    <div class="input-group">
                        <input type="search" class="form-control" name="q" value="{{ $q }}" placeholder="{{ __('Search projects, issues, people…') }}">
                        <button class="btn btn-primary" type="submit">{{ __('Search') }}</button>
                    </div>
                </form>
            </div>

            <div>
                @if ($q === '')
                    <p class="text-muted">{{ __('Type something to search.') }}</p>
                @else
                    <p class="text-muted">{{ __('Showing results for') }} <strong>{{ e($q) }}</strong></p>

                    <div class="row g-4 mt-2">
                        <div class="col-12 col-lg-6">
                            <h2 class="h6 mb-2">{{ __('Projects') }}</h2>
                            <div class="list-group">
                                @forelse ($projects as $p)
                                    <a class="list-group-item list-group-item-action" href="{{ route('projects.show', ['project' => $p]) }}">
                                        <strong>{{ $p->key }}</strong> — {{ $p->name }}
                                    </a>
                                @empty
                                    <div class="text-muted small px-3 py-2">{{ __('No projects.') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <h2 class="h6 mb-2">{{ __('Issues') }}</h2>
                            <div class="list-group">
                                @forelse ($issues as $i)
                                    <a class="list-group-item list-group-item-action" href="{{ route('issues.show', ['project' => $i->project, 'issue' => $i]) }}">
                                        <strong>{{ $i->key }}</strong> — {{ $i->summary }}
                                    </a>
                                @empty
                                    <div class="text-muted small px-3 py-2">{{ __('No issues.') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <h2 class="h6 mb-2">{{ __('Organizations') }}</h2>
                            <div class="list-group">
                                @forelse ($organizations as $o)
                                    <a class="list-group-item list-group-item-action" href="{{ route('organizations.show', $o) }}">
                                        {{ $o->name }}
                                    </a>
                                @empty
                                    <div class="text-muted small px-3 py-2">{{ __('No organizations.') }}</div>
                                @endforelse
                            </div>
                        </div>

                        <div class="col-12 col-lg-6">
                            <h2 class="h6 mb-2">{{ __('Goals') }}</h2>
                            <div class="list-group">
                                @forelse ($goals as $g)
                                    <a class="list-group-item list-group-item-action" href="{{ route('goals.show', $g) }}">
                                        {{ $g->title }}
                                    </a>
                                @empty
                                    <div class="text-muted small px-3 py-2">{{ __('No goals.') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

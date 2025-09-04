<?php
use App\Models\Project;
use App\Models\Issue;
use Illuminate\View\View;
use function Laravel\Folio\{name, middleware, render};

name('issues.create');
middleware(['auth','verified']);

render(function (View $view, Project $project) {
    $parent = null;
    $parentKey = request()->string('parent')->trim()->toString();
    if ($parentKey !== '') {
        $parent = Issue::query()
            ->where('project_id', $project->getKey())
            ->where('key', $parentKey)
            ->select(['id', 'key', 'summary', 'project_id'])
            ->first();
    }
    return $view->with(compact('project', 'parent'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ __('Create issue') }}</h2>
            <a href="{{ route('projects.show', ['project' => $project]) }}" class="link-primary">{{ __('Back to project') }}</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                    <livewire:issues.create-issue-form :project-id="$project->getKey()" :parent-id="$parent?->getKey()"/>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

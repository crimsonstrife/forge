<?php

use App\Models\Issue;
use App\Models\Project;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('issues.create.global');
middleware(['auth','verified']);

render(function (View $view) {
    $user = auth()->user();

    $project = null;
    $parentId = null;

    $projectId = request()->string('project')->trim()->toString();
    if ($projectId !== '') {
        $project = Project::query()
            ->select(['id','name','key'])
            ->whereKey($projectId)
            ->whereHas('users', fn ($q) => $q->whereKey($user->id))
            ->first();
    }

    if ($project) {
        $parentKey = request()->string('parent')->trim()->toString();
        if ($parentKey !== '') {
            $parentId = Issue::query()
                ->where('project_id', $project->getKey())
                ->where('key', $parentKey)
                ->value('id');
        }
    }

    $projectOptions = Project::query()
        ->select(['id','name','key'])
        ->whereHas('users', fn ($q) => $q->whereKey($user->id))
        ->orderBy('name')
        ->get();

    return $view->with(compact('project', 'projectOptions', 'parentId'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            Create issue
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl">
            <livewire:issues.create-issue-form
                :project-id="$project?->getKey()"
                :parent-id="$parentId"
                :project-options="$projectOptions->map(fn ($p) => ['id' => (string)$p->id, 'name' => $p->name, 'key' => $p->key])->all()"
            />
        </div>
    </div>
</x-app-layout>

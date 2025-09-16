<?php
use App\Models\Project;
use App\Models\Issue;
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

    return $view->with(compact('project','projectOptions','parentId'));
});
?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ __('Create issue') }}</h2>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                    <livewire:issues.create-issue-form
                        :project-id="$project?->getKey()"
                        :parent-id="$parentId"
                        :project-options="$projectOptions->map(fn ($p) => ['id' => (string)$p->id, 'name' => $p->name, 'key' => $p->key])->all()"
                    />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

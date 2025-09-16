<?php
use App\Models\Project;
use App\Models\Issue;
use function Laravel\Folio\{name, middleware};

name('issues.edit');
middleware(['auth','verified']);
?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ $project->key }} — {{ __('Edit') }} {{ $issue->key }}</h2>
            <a href="{{ route('issues.show', ['project' => $project, 'issue' => $issue]) }}" class="btn btn-outline-secondary">
                {{ __('Cancel') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-10 col-lg-8 col-xl-7">
                    <livewire:issues.update-issue-form :project="$project" :issue="$issue" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<?php

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use function Laravel\Folio\{name, middleware, render};

name('issues.focus');
middleware(['auth', 'verified']);

render(function (View $view, Project $project, Issue $issue): void {
    //
});

?>
<x-app-layout>
    <div class="p-4">
        <livewire:issues.focus-timer :issue="$issue" />
    </div>
</x-app-layout>

<?php

use App\Models\Goal;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('goals.edit');
middleware(['auth', 'verified']);

/** @param Goal $goal */
render(function (View $view, Goal $goal) {
    $goal->loadMissing(['keyResults']);
    return $view->with(compact('goal'));
});

?>
<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h1 class="h4 mb-0">Edit Goal</h1>
            <a href="{{ route('goals.show', ['goal' => $goal]) }}" class="btn btn-secondary">Back</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container mx-auto px-3" style="max-width: 720px">
            <livewire:goals.update-goal-form :goal="$goal" />
        </div>
    </div>
</x-app-layout>

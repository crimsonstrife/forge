<?php

use function Laravel\Folio\{name, middleware};

name('goals.create');
middleware(['auth', 'verified']);

?>
<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Create Goal</h1>
    </x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 720px">
            <livewire:goals.create-goal-form />
        </div>
    </div>
</x-app-layout>


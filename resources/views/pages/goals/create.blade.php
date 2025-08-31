<?php

use function Laravel\Folio\{name, middleware};

name('goals.create');
middleware(['auth', 'verified']);

?>
<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Create Goal</h1>
    </x-slot>

    <livewire:goals.create-goal-form />
</x-app-layout>


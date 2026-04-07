<?php
use function Laravel\Folio\{name, middleware};

name('organizations.create');
middleware(['auth','verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">New Organization</h2>
    </x-slot>

    <div class="py-4">
        <div class="container py-4">
            <livewire:organizations.form />
        </div>
    </div>
</x-app-layout>

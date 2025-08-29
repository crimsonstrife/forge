<?php
use function Laravel\Folio\{name, middleware};

name('organizations.create');
middleware(['auth', 'verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">New Organization</h2>
    </x-slot>

    <div class="py-6">
        <livewire:organizations.form />
    </div>
</x-app-layout>

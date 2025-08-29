<?php
use function Laravel\Folio\{name, middleware};

/** @var \App\Models\Organization $organization */
name('organizations.edit');
middleware(['auth', 'verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg">Edit Organization</h2>
            <a class="btn btn-sm btn-ghost"
               href="{{ route('organizations.show', ['organization' => $organization]) }}">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <livewire:organizations.form :organization="$organization" />
    </div>
</x-app-layout>

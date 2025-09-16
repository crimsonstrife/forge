<?php
use function Laravel\Folio\{name, middleware};
/** @var \App\Models\Organization $organization */
name('organizations.edit');
middleware(['auth','verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">Edit Organization</h2>
            <a class="btn btn-sm btn-outline-secondary"
               href="{{ route('organizations.show', ['organization' => $organization->slug]) }}">Back</a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 720px">
            <livewire:organizations.form :organization="$organization" />
        </div>
    </div>
</x-app-layout>

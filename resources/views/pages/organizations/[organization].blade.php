<?php

use App\Models\Organization;

use function Laravel\Folio\{name, middleware, render};

name('organizations.show');
middleware(['auth', 'verified']);

render(function (\Illuminate\View\View $view, Organization $organization) {
    // ...
});
?>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-lg">{{ $organization->name }}</h2>
            <div class="flex items-center gap-2">
                <a class="btn btn-sm"
                   href="{{ route('organizations.edit', ['organization' => $organization->slug]) }}">Edit</a>
                <a class="btn btn-sm btn-ghost" href="{{ route('organizations.index') }}">Back</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success my-4">{{ session('status') }}</div>
    @endif

    <div class="py-6">
        <div class="card p-6">
            <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <dt class="text-xs opacity-70">Name</dt>
                    <dd class="font-medium">{{ $organization->name }}</dd>
                </div>
                <div>
                    <dt class="text-xs opacity-70">Slug</dt>
                    <dd class="font-mono">{{ $organization->slug }}</dd>
                </div>
                <div>
                    <dt class="text-xs opacity-70">Created</dt>
                    <dd>{{ $organization->created_at->toDayDateTimeString() }}</dd>
                </div>
            </dl>
        </div>
    </div>
</x-app-layout>

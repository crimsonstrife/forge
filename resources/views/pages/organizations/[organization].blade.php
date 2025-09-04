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
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="h4 mb-0">{{ $organization->name }}</h2>
            <div class="d-flex gap-2">
                <a class="btn btn-sm btn-primary"
                   href="{{ route('organizations.edit', ['organization' => $organization->slug]) }}">Edit</a>
                <a class="btn btn-sm btn-outline-secondary"
                   href="{{ route('organizations.index') }}">Back</a>
            </div>
        </div>
    </x-slot>

    @if (session('status'))
        <div class="alert alert-success my-3">{{ session('status') }}</div>
    @endif>

    <div class="py-4">
        <div class="container" style="max-width: 800px">
            <div class="card shadow-sm">
                <div class="card-body">
                    <dl class="row row-cols-1 row-cols-sm-3 g-3 mb-0">
                        <div class="col">
                            <dt class="text-uppercase text-body-secondary small mb-1">Name</dt>
                            <dd class="mb-0 fw-medium">{{ $organization->name }}</dd>
                        </div>
                        <div class="col">
                            <dt class="text-uppercase text-body-secondary small mb-1">Slug</dt>
                            <dd class="mb-0 font-monospace">{{ $organization->slug }}</dd>
                        </div>
                        <div class="col">
                            <dt class="text-uppercase text-body-secondary small mb-1">Created</dt>
                            <dd class="mb-0">{{ $organization->created_at->toDayDateTimeString() }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

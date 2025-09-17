<?php

use function Laravel\Folio\{name, middleware};

name('support.staff.index');
middleware(['auth', 'verified']);

?>

<x-app-layout>
    <div class="container py-4">
        <h1 class="h4 mb-3">Support triage</h1>
        @can('viewAny', App\Models\Ticket::class)
            @livewire('staff.support.triage')
        @else
            <div class="alert alert-danger">You do not have permission to view support tickets.</div>
        @endcan
    </div>
</x-app-layout>

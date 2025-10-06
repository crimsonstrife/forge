<?php

use Illuminate\View\View;

use function Laravel\Folio\{name, middleware};

name('today.index');
middleware(['auth', 'verified']);

?>
<x-app-layout>
    <x-slot name="header">
        <h1 class="h4 mb-0">Today</h1>
    </x-slot>

    <div class="container py-3">
        <div class="row g-3">
            <div class="col-12 col-xl-8">
                <livewire:today.today-panel />
            </div>
            <div class="col-12 col-xl-4">
                <livewire:notes.quick-capture />
            </div>
        </div>
    </div>
</x-app-layout>

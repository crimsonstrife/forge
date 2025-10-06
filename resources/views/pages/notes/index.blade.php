<?php

use function Laravel\Folio\{name, middleware};

name('notes.index');
middleware(['auth','verified']);
?>
<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Notes</h1></x-slot>
    <div class="container py-3">
        <div class="row g-3">
            <div class="col-12 col-xl-6">
                {{-- No project passed → shows project picker for Convert --}}
                <livewire:notes.quick-capture />
            </div>
        </div>
    </div>
</x-app-layout>

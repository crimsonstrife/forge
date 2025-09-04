<?php
use function Laravel\Folio\{name, middleware};

name('organizations.index');
middleware(['auth','verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 mb-0">Organizations</h2>
    </x-slot>

    <div class="py-4">
        <div class="container" style="max-width: 1000px">
            <livewire:organizations.index />
        </div>
    </div>
</x-app-layout>

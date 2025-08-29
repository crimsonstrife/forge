<?php
use function Laravel\Folio\{name, middleware};

name('organizations.index');
middleware(['auth', 'verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Organizations</h2>
    </x-slot>

    <div class="py-6">
        <livewire:organizations.index />
    </div>
</x-app-layout>

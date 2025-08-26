<?php

use function Laravel\Folio\{name, middleware};

name('projects.show');
middleware(['auth', 'verified']);

?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ $project->key }} — {{ $project->name }}</h2>
    </x-slot>

    <div class="py-6">
        {{-- $project is available automatically because of [project].blade.php --}}
    </div>
</x-app-layout>

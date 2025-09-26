<?php

use function Laravel\Folio\{name, middleware};

name('status');
middleware(['auth', 'verified']);
?>
<x-app-layout>
    <x-slot name="header"><h1 class="h3 mb-0">Status</h1></x-slot>

    <div class="container py-4">
        @include('vendor.health.list')
    </div>
</x-app-layout>

<?php

use function Laravel\Folio\{name, middleware};

name('support.index');

?>

<x-app-layout>
    <div class="container py-4">
        <h1 class="h4 mb-3">Support</h1>
        <p class="text-body-secondary">Submit a request or review your existing tickets.</p>
        <div class="d-flex gap-2">
            <a class="btn btn-primary" href="{{ route('support.new') }}">Submit a ticket</a>
            <a class="btn btn-outline-secondary" href="{{ route('support.access.request') }}">Access my tickets</a>
        </div>
    </div>
</x-app-layout>

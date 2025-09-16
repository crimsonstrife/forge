<?php

use function Laravel\Folio\{name};

name('support.access.request');

?>

<x-app-layout>
    <div class="container py-4">
        <h1 class="h4 mb-3">Access your tickets</h1>
        <p class="mb-3">Use the magic link we emailed to you when you created a ticket. If you just submitted, you can also follow the link shown on the confirmation page.</p>
        <a class="btn btn-secondary" href="{{ route('support.index') }}">Back to Support</a>
    </div>
</x-app-layout>

<?php

use function Laravel\Folio\{name, middleware};

name('support.my');
middleware(['support.identity']);

?>

<x-guest-layout>
    <div class="container py-4">
        <h1 class="h4 mb-3">My tickets</h1>
        @livewire('support.my-tickets')
    </div>
</x-guest-layout>

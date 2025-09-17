<?php

use function Laravel\Folio\{name, middleware};

name('support.ticket.public');
middleware(['support.identity']);

?>

<x-guest-layout>
    <div class="container py-4">
        @livewire('support.show-ticket', ['key' => $key])
    </div>
</x-guest-layout>

<?php

use function Laravel\Folio\{name, middleware};

name('support.ticket.public');
middleware(['support.identity']);

?>

<x-app-layout>
    <div class="container py-4">
        @livewire('support.show-ticket', ['key' => $key])
    </div>
</x-app-layout>

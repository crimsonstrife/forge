<?php

use function Laravel\Folio\{name, middleware};

name('support.staff.show');
middleware(['auth', 'verified']);

?>

<x-app-layout>
    <div class="container py-4">
        @livewire('staff.support.show-ticket', ['key' => $key])
    </div>
</x-app-layout>
<?php

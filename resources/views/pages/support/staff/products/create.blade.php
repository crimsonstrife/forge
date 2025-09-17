<?php

use function Laravel\Folio\{name, middleware};

name('support.staff.products.create');
middleware(['auth','verified']);

?>

<x-app-layout>
    <div class="container py-4">
        @livewire('staff.support.products.create')
    </div>
</x-app-layout>

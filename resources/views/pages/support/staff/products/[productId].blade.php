<?php

use function Laravel\Folio\{name, middleware};

name('support.staff.products.edit');
middleware(['auth','verified']);

?>

<x-app-layout>
    <div class="container py-4">
        @livewire('staff.support.products.edit', ['productId' => $productId])
    </div>
</x-app-layout>

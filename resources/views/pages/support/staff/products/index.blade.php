<?php

use function Laravel\Folio\{name, middleware};

name('support.staff.products.index');
middleware(['auth','verified']);

?>

<x-app-layout>
    <div class="container py-4">
        <h1 class="h4 mb-3">Support Products</h1>
        @can('support.manage')
            @livewire('staff.support.products.index')
        @else
            <div class="alert alert-danger">You do not have permission to manage products.</div>
        @endcan
    </div>
</x-app-layout>

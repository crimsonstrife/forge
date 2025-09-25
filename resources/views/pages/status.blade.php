<?php
use function Laravel\Folio\name;
name('status');
?>
<x-app-layout>
    <x-slot name="header"><h1 class="h3 mb-0">Status</h1></x-slot>
    <div class="container mx-auto py-4">
        <div class="alert alert-success mb-4">
            All systems operational.
        </div>
        <p class="text-body-secondary mb-0">This is a simple placeholder.</p>
    </div>
</x-app-layout>

<?php
use function Laravel\Folio\name;

name('legal.terms.show');
?>

<x-guest-layout>
    <x-slot name="header">
        <h1 class="h3 mb-0">Terms of Service</h1>
    </x-slot>

    <div class="container mx-auto py-4">
        <x-markdown>
            @php
                echo file_get_contents(resource_path('markdown/terms.md'))
            @endphp
        </x-markdown>
    </div>
</x-guest-layout>

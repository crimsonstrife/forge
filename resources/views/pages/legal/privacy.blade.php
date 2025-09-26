<?php
use function Laravel\Folio\name;

name('legal.policy.show');
?>

<x-guest-layout>
    <x-slot name="header">
        <h1 class="h3 mb-0">Privacy Policy</h1>
    </x-slot>

    <div class="container mx-auto py-4">
        <x-markdown>
            @php
                echo file_get_contents(resource_path('markdown/policy.md'))
            @endphp
        </x-markdown>
    </div>
</x-guest-layout>

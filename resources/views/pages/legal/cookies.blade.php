<?php
use function Laravel\Folio\name;
name('legal.cookies.show');
?>

<x-guest-layout>
    <x-slot name="header">
        <h1 class="h3 mb-0">Cookie Policy</h1>
    </x-slot>

    <div class="container py-4">
        <x-markdown>
            @php
                echo file_get_contents(resource_path('markdown/cookies.md'))
            @endphp
        </x-markdown>
    </div>
</x-guest-layout>

<?php
use function Laravel\Folio\name;

name('changelog');
?>
<x-app-layout>
    <x-slot name="header"><h1 class="h3 mb-0">Changelog</h1></x-slot>
    <div class="container mx-auto py-4">
        <x-markdown>
            @php
                echo file_get_contents(resource_path('markdown/changelog.md'));
                if (file_get_contents(resource_path('markdown/changelog.md')) === "") {
                    echo "# Changelog\n\n_No releases yet. Add entries to `resources/markdown/changelog.md`_.";
                }
            @endphp
        </x-markdown>
    </div>
</x-app-layout>

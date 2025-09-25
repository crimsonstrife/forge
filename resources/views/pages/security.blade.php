<?php
use function Laravel\Folio\name;
name('security');
?>
<x-app-layout>
    <x-slot name="header"><h1 class="h3 mb-0">Security</h1></x-slot>
    <div class="container py-4">
        <x-markdown>
            We take application security seriously. If you believe you've found a vulnerability,
            please email **security@crimsonstrife.live** with details.
        </x-markdown>
    </div>
</x-app-layout>

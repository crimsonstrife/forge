<?php

use function Laravel\Folio\{name};

name('support.new');

?>

<x-app-layout>
    <div class="container py-4">
        <h1 class="h4 mb-3">Submit a ticket</h1>
        <div class="alert alert-warning small">
            {{ app(\App\Settings\SupportSettings::class)->public_warning_text }}
        </div>
        @livewire('support.new-ticket')
    </div>
</x-app-layout>

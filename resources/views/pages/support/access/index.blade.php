<?php

use App\Models\SupportIdentity;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function Laravel\Folio\{name, render};

name('support.access.request');

render(function (View $view, Request $request) {
    $cookieId = $request->cookie('support_identity');
    $identity = is_string($cookieId)
        ? SupportIdentity::query()->whereKey($cookieId)->whereNull('revoked_at')->first()
        : null;

    return $view->with(compact('identity'));
});

?>

<x-guest-layout>
    <div class="container mx-auto py-4">
        @if($identity)
            <div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
                <div>
                    <h1 class="h4 mb-1">My support tickets</h1>
                    <p class="text-body-secondary mb-0">Tickets linked to your current support access cookie.</p>
                </div>
                <div class="d-flex gap-2">
                    <a class="btn btn-primary" href="{{ route('support.new') }}">Submit a ticket</a>
                    <a class="btn btn-outline-secondary" href="{{ route('support.index') }}">Support Portal</a>
                </div>
            </div>

            @livewire('support.my-tickets', ['identityId' => $identity->getKey()])
        @else
            <h1 class="h4 mb-3">Access your tickets</h1>
            <p class="mb-3">Use the magic link we emailed to you when you created a ticket. If you already opened that link on this device, your tickets will appear here automatically.</p>
            <div class="d-flex gap-2">
                <a class="btn btn-primary" href="{{ route('support.index') }}">Back to Support</a>
                <a class="btn btn-outline-secondary" href="{{ route('support.new') }}">Submit a ticket</a>
            </div>
        @endif
    </div>
</x-guest-layout>

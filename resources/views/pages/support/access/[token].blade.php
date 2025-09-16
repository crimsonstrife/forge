<?php

use App\Models\SupportIdentity;
use Illuminate\Http\Response;
use Illuminate\View\View;
use function Laravel\Folio\{name, render};

name('support.access.by-token');

render(function (View $view, string $token) {
    /** @var SupportIdentity|null $identity */
    $identity = SupportIdentity::query()
        ->where('token', $token)
        ->whereNull('revoked_at')
        ->first();

    abort_if($identity === null, 404);

    $identity->forceFill(['last_seen_at' => now()])->save();

    return new Response(view: $view->make('pages.support.access.success'), status: 302, headers: [
        'Location' => route('support.my'),
    ], cookies: [
        cookie()->forever('support_identity', $identity->getKey(), secure: true, httpOnly: true, sameSite: 'lax'),
    ]);
});


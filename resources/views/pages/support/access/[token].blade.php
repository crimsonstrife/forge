<?php

use App\Models\SupportIdentity;
use Illuminate\Http\RedirectResponse;

use function Laravel\Folio\{name, render};

name('support.access.by-token');

render(function (string $token): RedirectResponse {
    $identity = SupportIdentity::query()
        ->where('token', $token)
        ->whereNull('revoked_at')
        ->firstOrFail();

    $identity->forceFill(['last_seen_at' => now()])->save();

    return redirect()->route('support.my')
        ->withCookie(
            cookie()->forever(
                name: 'support_identity',
                value: $identity->getKey(),
                secure: true,
                httpOnly: true,
                sameSite: 'lax',
            )
        );
});

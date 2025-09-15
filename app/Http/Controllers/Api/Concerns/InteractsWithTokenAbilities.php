<?php

namespace App\Http\Controllers\Api\Concerns;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

trait InteractsWithTokenAbilities
{
    protected function requireAbility(Request $request, string $ability): void
    {
        if ($request->user()?->currentAccessToken() === null) {
            abort(Response::HTTP_UNAUTHORIZED, 'Missing access token.');
        }

        if (! $request->user()->tokenCan($ability)) {
            abort(Response::HTTP_FORBIDDEN, "Token missing ability: {$ability}");
        }
    }
}

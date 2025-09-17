<?php

namespace App\Http\Middleware;

use App\Models\SupportIdentity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureSupportIdentity
{
    public function handle(Request $request, Closure $next): Response
    {
        $cookieId = $request->cookie('support_identity');

        if (is_string($cookieId)) {
            $identity = SupportIdentity::query()
                ->whereKey($cookieId)
                ->whereNull('revoked_at')
                ->first();

            if ($identity !== null) {
                app()->instance(SupportIdentity::class, $identity);
                return $next($request);
            }
        }

        $token = (string) $request->query('token', '');
        if ($token !== '') {
            $identity = SupportIdentity::query()
                ->where('token', $token)
                ->whereNull('revoked_at')
                ->first();

            if ($identity !== null) {
                $identity->forceFill(['last_seen_at' => now()])->save();

                app()->instance(SupportIdentity::class, $identity);

                return $next($request)->withCookie(
                    cookie()->forever('support_identity', $identity->getKey(), secure: true, httpOnly: true, sameSite: 'lax')
                );
            }
        }

        return redirect()->route('support.access.request');
    }
}

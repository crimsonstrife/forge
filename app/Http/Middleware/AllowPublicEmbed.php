<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowPublicEmbed
{
    /** @param Closure(Request):Response $next */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->route()?->getName() === 'embed.projects.show') {
            /** @var array<int, string> $allow */
            $allow = config('security.embed_allowlist', []);
            if (! empty($allow)) {
                $response->headers->set('Content-Security-Policy', "frame-ancestors " . implode(' ', $allow));
            }
        }

        return $response;
    }
}

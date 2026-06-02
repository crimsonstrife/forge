<?php

namespace App\Http\Middleware;

use App\Models\FeedbackIdentity;
use App\Services\Feedback\FeedbackSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOptionalFeedbackIdentity
{
    public function handle(Request $request, Closure $next): Response
    {
        $header = (string) $request->header('Authorization', '');
        $identity = str_starts_with($header, 'Bearer ')
            ? app(FeedbackSessionService::class)->resolveBearer(substr($header, 7), $request)
            : null;

        if ($identity instanceof FeedbackIdentity) {
            $request->attributes->set('feedback_identity', $identity);
            app()->instance(FeedbackIdentity::class, $identity);
        }

        return $next($request);
    }
}

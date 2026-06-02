<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\AuthVerifyRequest;
use App\Http\Resources\Api\V1\Feedback\FeedbackIdentityResource;
use App\Services\Feedback\FeedbackSessionService;
use App\Services\Feedback\MagicLinkService;
use Illuminate\Http\JsonResponse;

class AuthVerifyController extends Controller
{
    public function __construct(
        private MagicLinkService $magicLinks,
        private FeedbackSessionService $sessions,
    ) {}

    /**
     * @group Feedback
     */
    public function __invoke(AuthVerifyRequest $request): JsonResponse
    {
        $result = $this->magicLinks->verifyToken(
            $request->string('token')->toString(),
            $request->filled('display_name') ? $request->string('display_name')->toString() : null,
        );

        if ($result->requiresDisplayName) {
            return response()->json(['requires_display_name' => true], 409);
        }

        $session = $this->sessions->issue($result->identity, $request);

        return response()->json([
            'session_token' => $session['token'],
            'expires_at' => $session['expires_at']->toIso8601String(),
            'identity' => FeedbackIdentityResource::make($result->identity)->resolve($request),
        ]);
    }
}

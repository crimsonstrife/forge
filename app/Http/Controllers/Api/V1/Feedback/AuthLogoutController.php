<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Services\Feedback\FeedbackSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthLogoutController extends Controller
{
    public function __construct(private FeedbackSessionService $sessions) {}

    /**
     * @group Feedback
     */
    public function __invoke(Request $request): JsonResponse
    {
        $bearer = str($request->header('Authorization', ''))->after('Bearer ')->toString();
        $this->sessions->revoke($bearer);

        return response()->json(['message' => 'Logged out.']);
    }
}

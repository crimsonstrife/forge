<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Feedback\AuthRequestRequest;
use App\Services\Feedback\FeedbackBoardResolver;
use App\Services\Feedback\MagicLinkService;
use Illuminate\Http\JsonResponse;

class AuthRequestController extends Controller
{
    public function __construct(
        private MagicLinkService $magicLinks,
        private FeedbackBoardResolver $boards,
    ) {}

    /**
     * @group Feedback
     */
    public function __invoke(AuthRequestRequest $request): JsonResponse
    {
        $board = $this->boards->forRequest($request, $request->string('board_slug')->toString());

        $this->magicLinks->requestForEmail($request->string('email')->toString(), $board, $request);

        return response()->json([
            'message' => 'If that email can sign in, a magic link has been sent.',
        ], 202);
    }
}

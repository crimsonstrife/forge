<?php

namespace App\Http\Controllers\Api\V1\Feedback;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\Feedback\FeedbackBoardResource;
use App\Models\FeedbackIdentity;
use App\Services\Feedback\FeedbackBoardResolver;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function __construct(private FeedbackBoardResolver $boards) {}

    /**
     * @group Feedback
     */
    public function show(Request $request, string $slug): FeedbackBoardResource
    {
        $board = $this->boards->forRequest($request, $slug);
        $identity = $request->attributes->get('feedback_identity');

        if (! $board->allow_anonymous_read && ! $identity instanceof FeedbackIdentity) {
            abort(401, 'A valid feedback session is required.');
        }

        return FeedbackBoardResource::make($board->load(['product:id,name', 'categories', 'statuses']));
    }
}

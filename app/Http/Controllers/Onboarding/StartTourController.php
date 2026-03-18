<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Models\UserTourState;
use App\Support\Onboarding\TourRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StartTourController extends Controller
{
    public function __invoke(Request $request, string $tour): JsonResponse
    {
        abort_unless(TourRegistry::has($tour), 404);

        $user = $request->user();
        $steps = TourRegistry::steps($tour, $user);

        abort_if($steps === [], 404);

        $state = UserTourState::query()->firstOrNew([
            'user_id' => $user->getKey(),
            'tour' => $tour,
        ]);

        $state->status = UserTourState::STATUS_ACTIVE;
        $state->last_step = 0;
        $state->started_at ??= now();
        $state->snoozed_until = null;
        $state->dismissed_at = null;
        $state->completed_at = null;
        $state->save();

        return response()->json([
            'state' => $state->toResponsePayload(),
            'step' => $steps[0],
        ]);
    }
}

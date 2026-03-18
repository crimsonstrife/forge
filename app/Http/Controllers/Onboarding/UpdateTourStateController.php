<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Models\UserTourState;
use App\Support\Onboarding\TourRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UpdateTourStateController extends Controller
{
    public function __invoke(Request $request, string $tour): JsonResponse
    {
        abort_unless(TourRegistry::has($tour), 404);

        $user = $request->user();
        $steps = TourRegistry::steps($tour, $user);

        abort_if($steps === [], 404);

        $data = $request->validate([
            'action' => ['required', Rule::in(['set-step', 'snooze', 'dismiss', 'complete'])],
            'lastStep' => ['nullable', 'integer', 'min:0'],
        ]);

        $state = UserTourState::query()->firstOrCreate(
            [
                'user_id' => $user->getKey(),
                'tour' => $tour,
            ],
            [
                'status' => UserTourState::STATUS_PENDING,
            ]
        );

        $maxStep = count($steps) - 1;
        $action = $data['action'];
        $lastStep = Arr::get($data, 'lastStep');

        switch ($action) {
            case 'set-step':
                $state->status = UserTourState::STATUS_ACTIVE;
                $state->last_step = $this->validatedStep($lastStep, $maxStep);
                $state->started_at ??= now();
                $state->snoozed_until = null;
                $state->dismissed_at = null;
                $state->completed_at = null;
                break;

            case 'snooze':
                $state->status = UserTourState::STATUS_SNOOZED;
                $state->snoozed_until = now()->addDays(3);
                $state->dismissed_at = null;
                $state->completed_at = null;
                break;

            case 'dismiss':
                $state->status = UserTourState::STATUS_DISMISSED;
                $state->dismissed_at = now();
                $state->snoozed_until = null;
                $state->completed_at = null;
                break;

            case 'complete':
                $state->status = UserTourState::STATUS_COMPLETED;
                $state->last_step = $lastStep === null ? $maxStep : $this->validatedStep($lastStep, $maxStep);
                $state->completed_at = now();
                $state->snoozed_until = null;
                $state->dismissed_at = null;
                break;
        }

        $state->save();

        return response()->json([
            'state' => $state->toResponsePayload(),
            'step' => $state->last_step !== null ? $steps[$state->last_step] : null,
        ]);
    }

    private function validatedStep(mixed $step, int $maxStep): int
    {
        if (! is_int($step) || $step < 0 || $step > $maxStep) {
            throw ValidationException::withMessages([
                'lastStep' => __('The selected tour step is invalid.'),
            ]);
        }

        return $step;
    }
}

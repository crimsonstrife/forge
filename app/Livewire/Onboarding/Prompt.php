<?php

namespace App\Livewire\Onboarding;

use App\Models\UserTourState;
use App\Support\Onboarding\TourRegistry;
use Illuminate\View\View;
use Livewire\Component;

class Prompt extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $config = [
        'enabled' => false,
    ];

    public function mount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->hasVerifiedEmail()) {
            return;
        }

        $currentRoute = request()->route()?->getName();
        $tour = $this->resolvedTourName($user->getKey());
        $tourDefinition = TourRegistry::definition($tour, $user);

        if ($tourDefinition === []) {
            return;
        }

        $state = $this->resolvedState($user->getKey(), $tour, $currentRoute);

        $this->config = [
            'enabled' => true,
            'currentRoute' => $currentRoute,
            'shouldPrompt' => $tour === TourRegistry::MAIN_APP
                && $currentRoute === 'dashboard'
                && $this->shouldPrompt($state),
            'messages' => $this->messages(),
            'tour' => [
                ...$tourDefinition,
                'state' => $this->statePayload($state),
            ],
        ];
    }

    public function render(): View
    {
        return view('livewire.onboarding.prompt');
    }

    private function shouldPrompt(?UserTourState $state): bool
    {
        if ($state === null) {
            return true;
        }

        return match ($state->status) {
            UserTourState::STATUS_PENDING => true,
            UserTourState::STATUS_SNOOZED => $state->snoozed_until === null || $state->snoozed_until->isPast(),
            default => false,
        };
    }

    /**
     * @return array<string, int|string|null>
     */
    private function statePayload(?UserTourState $state): array
    {
        return $state?->toResponsePayload() ?? UserTourState::defaultResponsePayload();
    }

    /**
     * @return array<string, string>
     */
    private function messages(): array
    {
        return [
            'stepCounter' => __('onboarding.ui.step_counter'),
            'next' => __('onboarding.ui.next'),
            'back' => __('onboarding.ui.back'),
            'finish' => __('onboarding.ui.finish'),
            'openPage' => __('onboarding.ui.open_page'),
            'endTour' => __('onboarding.ui.end_tour'),
            'dismissAriaLabel' => __('onboarding.ui.dismiss_aria_label'),
            'continueToPage' => __('onboarding.ui.continue_to_page'),
            'collapsedNavigationHint' => __('onboarding.ui.collapsed_navigation_hint'),
        ];
    }

    private function resolvedTourName(string $userId): string
    {
        return UserTourState::query()
            ->where('user_id', $userId)
            ->where('status', UserTourState::STATUS_ACTIVE)
            ->whereIn('tour', TourRegistry::names())
            ->latest('updated_at')
            ->value('tour') ?? TourRegistry::MAIN_APP;
    }

    private function resolvedState(string $userId, string $tour, ?string $currentRoute): ?UserTourState
    {
        if ($tour === TourRegistry::MAIN_APP && $currentRoute === 'dashboard') {
            return UserTourState::query()->firstOrCreate(
                [
                    'user_id' => $userId,
                    'tour' => $tour,
                ],
                [
                    'status' => UserTourState::STATUS_PENDING,
                ]
            );
        }

        return UserTourState::query()
            ->where('user_id', $userId)
            ->where('tour', $tour)
            ->first();
    }
}

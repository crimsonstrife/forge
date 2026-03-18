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

        $tour = TourRegistry::MAIN_APP;
        $currentRoute = request()->route()?->getName();
        $steps = TourRegistry::steps($tour, $user);

        if ($steps === []) {
            return;
        }

        $state = $currentRoute === 'dashboard'
            ? UserTourState::query()->firstOrCreate(
                [
                    'user_id' => $user->getKey(),
                    'tour' => $tour,
                ],
                [
                    'status' => UserTourState::STATUS_PENDING,
                ]
            )
            : UserTourState::query()
                ->where('user_id', $user->getKey())
                ->where('tour', $tour)
                ->first();

        $this->config = [
            'enabled' => true,
            'currentRoute' => $currentRoute,
            'shouldPrompt' => $currentRoute === 'dashboard' && $this->shouldPrompt($state),
            'messages' => $this->messages(),
            'tour' => [
                'name' => $tour,
                'label' => __('onboarding.tours.main_app.label'),
                'routes' => [
                    'start' => route('onboarding.tours.start', ['tour' => $tour]),
                    'update' => route('onboarding.tours.update', ['tour' => $tour]),
                ],
                'state' => $this->statePayload($state),
                'steps' => $steps,
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
}

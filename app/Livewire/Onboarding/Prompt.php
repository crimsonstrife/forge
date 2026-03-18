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
            'tour' => [
                'name' => $tour,
                'label' => 'Main app',
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
        if ($state === null) {
            return [
                'status' => UserTourState::STATUS_PENDING,
                'lastStep' => null,
            ];
        }

        return [
            'status' => $state->status,
            'lastStep' => $state->last_step,
        ];
    }
}

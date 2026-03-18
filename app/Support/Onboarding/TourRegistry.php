<?php

namespace App\Support\Onboarding;

use App\Models\User;
use Laravel\Jetstream\Jetstream;

final class TourRegistry
{
    public const MAIN_APP = 'main-app';

    /**
     * @return array<string, array{route: string, selector: string, placement: string}>
     */
    private static function mainAppBlueprint(): array
    {
        $steps = [
            'dashboard' => [
                'route' => 'dashboard',
                'selector' => '[data-tour="dashboard-overview"]',
                'placement' => 'bottom',
            ],
            'issues' => [
                'route' => 'dashboard',
                'selector' => '[data-tour="issues-overview"]',
                'placement' => 'right',
            ],
            'projects' => [
                'route' => 'projects.index',
                'selector' => '[data-tour="projects-page"]',
                'placement' => 'bottom',
            ],
            'goals' => [
                'route' => 'goals.index',
                'selector' => '[data-tour="goals-page"]',
                'placement' => 'bottom',
            ],
            'support' => [
                'route' => 'support.staff.index',
                'selector' => '[data-tour="support-page"]',
                'placement' => 'bottom',
            ],
            'search' => [
                'route' => 'search',
                'selector' => '[data-tour="search-page"]',
                'placement' => 'bottom',
            ],
            'create' => [
                'route' => 'dashboard',
                'selector' => '[data-tour="create-menu"]',
                'placement' => 'left',
            ],
        ];

        if (Jetstream::hasTeamFeatures()) {
            $steps['teams'] = [
                'route' => 'dashboard',
                'selector' => '[data-tour="teams-menu"]',
                'placement' => 'left',
            ];
        }

        $steps['account'] = [
            'route' => 'dashboard',
            'selector' => '[data-tour="account-menu"]',
            'placement' => 'left',
        ];

        return $steps;
    }

    public static function has(string $tour): bool
    {
        return in_array($tour, [self::MAIN_APP], true);
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function steps(string $tour, ?User $user = null): array
    {
        return match ($tour) {
            self::MAIN_APP => self::mainApp($user),
            default => [],
        };
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function mainApp(?User $user = null): array
    {
        unset($user);

        $steps = [];

        foreach (self::mainAppBlueprint() as $key => $definition) {
            $steps[] = self::resolveStep(
                $definition,
                "onboarding.tours.main_app.steps.{$key}"
            );
        }

        return $steps;
    }

    /**
     * @param  array{route: string, selector: string, placement: string}  $definition
     * @return array<string, string>
     */
    private static function resolveStep(array $definition, string $translationKey): array
    {
        return [
            'route' => $definition['route'],
            'url' => route($definition['route']),
            'selector' => $definition['selector'],
            'title' => __($translationKey.'.title'),
            'body' => __($translationKey.'.body'),
            'placement' => $definition['placement'],
        ];
    }
}

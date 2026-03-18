<?php

namespace App\Support\Onboarding;

use App\Models\User;
use Laravel\Jetstream\Jetstream;

final class TourRegistry
{
    public const MAIN_APP = 'main-app';

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

        $steps = [
            [
                'route' => 'dashboard',
                'url' => route('dashboard'),
                'selector' => '[data-tour="dashboard-overview"]',
                'title' => 'Your dashboard',
                'body' => 'Start here for assigned work, due dates, and recent activity across Forge.',
                'placement' => 'bottom',
            ],
            [
                'route' => 'dashboard',
                'url' => route('dashboard'),
                'selector' => '[data-tour="issues-overview"]',
                'title' => 'Issues keep work moving',
                'body' => 'Issues are the core units of work in Forge, and this panel keeps your assigned items close at hand.',
                'placement' => 'right',
            ],
            [
                'route' => 'projects.index',
                'url' => route('projects.index'),
                'selector' => '[data-tour="projects-page"]',
                'title' => 'Projects',
                'body' => 'Projects organize delivery, planning, and issue workflows for each initiative.',
                'placement' => 'bottom',
            ],
            [
                'route' => 'goals.index',
                'url' => route('goals.index'),
                'selector' => '[data-tour="goals-page"]',
                'title' => 'Goals',
                'body' => 'Goals keep longer-term outcomes visible across projects and issues.',
                'placement' => 'bottom',
            ],
            [
                'route' => 'support.staff.index',
                'url' => route('support.staff.index'),
                'selector' => '[data-tour="support-page"]',
                'title' => 'Service desk',
                'body' => 'Support tickets help staff triage inbound requests and connect them back to internal work.',
                'placement' => 'bottom',
            ],
            [
                'route' => 'search',
                'url' => route('search'),
                'selector' => '[data-tour="search-page"]',
                'title' => 'Search',
                'body' => 'Search is the fastest way to jump to projects, issues, organizations, and goals.',
                'placement' => 'bottom',
            ],
            [
                'route' => 'dashboard',
                'url' => route('dashboard'),
                'selector' => '[data-tour="create-menu"]',
                'title' => 'Create from anywhere',
                'body' => 'Use Create to add new issues, projects, organizations, and goals without leaving the current page.',
                'placement' => 'left',
            ],
        ];

        if (Jetstream::hasTeamFeatures()) {
            $steps[] = [
                'route' => 'dashboard',
                'url' => route('dashboard'),
                'selector' => '[data-tour="teams-menu"]',
                'title' => 'Teams',
                'body' => 'Switch teams, manage membership, and open team settings from here.',
                'placement' => 'left',
            ];
        }

        $steps[] = [
            'route' => 'dashboard',
            'url' => route('dashboard'),
            'selector' => '[data-tour="account-menu"]',
            'title' => 'Account and help',
            'body' => 'Manage your profile, API tokens, and relaunch this tour or the Getting Started guide later.',
            'placement' => 'left',
        ];

        return $steps;
    }
}

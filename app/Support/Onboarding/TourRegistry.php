<?php

namespace App\Support\Onboarding;

use App\Models\User;
use App\Support\Codex\CodexConnection;
use Laravel\Jetstream\Jetstream;

final class TourRegistry
{
    public const MAIN_APP = 'main-app';

    public const PROJECT_DETAIL = 'project-detail';

    public const ISSUE_DETAIL = 'issue-detail';

    /**
     * @return array<int, string>
     */
    public static function names(): array
    {
        return [
            self::MAIN_APP,
            self::PROJECT_DETAIL,
            self::ISSUE_DETAIL,
        ];
    }

    public static function has(string $tour): bool
    {
        return in_array($tour, self::names(), true);
    }

    /**
     * @return array{name: string, label: string, routes: array{start: string, update: string}, steps: array<int, array<string, string>>}|array{}
     */
    public static function definition(string $tour, ?User $user = null): array
    {
        $steps = self::steps($tour, $user);

        if ($steps === []) {
            return [];
        }

        return [
            'name' => $tour,
            'label' => __("onboarding.tours.{$tour}.label"),
            'routes' => [
                'start' => route('onboarding.tours.start', ['tour' => $tour]),
                'update' => route('onboarding.tours.update', ['tour' => $tour]),
            ],
            'steps' => $steps,
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    public static function steps(string $tour, ?User $user = null): array
    {
        return match ($tour) {
            self::MAIN_APP => self::mainApp(),
            self::PROJECT_DETAIL => self::projectDetail($user),
            self::ISSUE_DETAIL => self::issueDetail($user),
            default => [],
        };
    }

    /**
     * @return array<string, array{route: string, selector: string, placement: string}>
     */
    private static function mainAppBlueprint(): array
    {
        $steps = [
            'dashboard' => [
                'route' => 'dashboard',
                'selector' => '[data-tour="dashboard-overview"]',
                'placement' => 'top',
            ],
            'workspaces' => [
                'route' => 'dashboard',
                'selector' => '[data-tour="dashboard-workspaces"]',
                'placement' => 'right',
            ],
            'dashboard_layout' => [
                'route' => 'dashboard',
                'selector' => '[data-tour="dashboard-layout-controls"]',
                'placement' => 'top',
            ],
            'issues_nav' => [
                'route' => 'dashboard',
                'selector' => '[data-tour="issues-nav"]',
                'placement' => 'bottom',
            ],
            'issue_explorer' => [
                'route' => 'issues.explorer',
                'selector' => '[data-tour="issues-explorer-query"]',
                'placement' => 'left',
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
                'route' => 'search',
                'selector' => '[data-tour="support-nav"]',
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

    /**
     * @return array<int, array<string, string>>
     */
    private static function mainApp(): array
    {
        $steps = [];

        foreach (self::mainAppBlueprint() as $key => $definition) {
            $steps[] = self::resolveStep(
                $definition,
                "onboarding.tours.main-app.steps.{$key}"
            );
        }

        return $steps;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function projectDetail(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $sandbox = app(SandboxProjectProvisioner::class)->sandboxFor($user);
        $project = $sandbox['project'];

        $steps = [];
        $blueprint = [
            'header' => [
                'route' => 'projects.show',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="project-overview-header"]',
                'placement' => 'bottom',
            ],
            'tabs' => [
                'route' => 'projects.show',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="project-nav-tabs"]',
                'placement' => 'bottom',
            ],
            'status_summary' => [
                'route' => 'projects.show',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="project-status-summary"]',
                'placement' => 'right',
            ],
            'assigned_issues' => [
                'route' => 'projects.show',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="project-assigned-issues"]',
                'placement' => 'right',
            ],
            'activity' => [
                'route' => 'projects.show',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="project-activity"]',
                'placement' => 'left',
            ],
            'sidebar' => [
                'route' => 'projects.show',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="project-sidebar"]',
                'placement' => 'left',
            ],
            'backlog_controls' => [
                'route' => 'projects.backlog',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="backlog-planning-controls"]',
                'placement' => 'bottom',
            ],
            'backlog_queue' => [
                'route' => 'projects.backlog',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="backlog-prioritized-list"]',
                'placement' => 'right',
            ],
            'backlog_sprints' => [
                'route' => 'projects.backlog',
                'routeParameters' => ['project' => $project],
                'selector' => '[data-tour="backlog-sprint-plan"]',
                'placement' => 'left',
            ],
        ];

        foreach ($blueprint as $key => $definition) {
            $steps[] = self::resolveStep(
                $definition,
                "onboarding.tours.project-detail.steps.{$key}"
            );
        }

        return $steps;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private static function issueDetail(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $sandbox = app(SandboxProjectProvisioner::class)->sandboxFor($user);
        $project = $sandbox['project'];
        $issue = $sandbox['issue'];
        $projectRepo = $project->repositoryLink?->repository;

        $steps = [];
        $blueprint = [
            'actions' => [
                'route' => 'issues.show',
                'routeParameters' => ['project' => $project, 'issue' => $issue],
                'selector' => '[data-tour="issue-actions"]',
                'placement' => 'bottom',
            ],
            'header' => [
                'route' => 'issues.show',
                'routeParameters' => ['project' => $project, 'issue' => $issue],
                'selector' => '[data-tour="issue-overview-header"]',
                'placement' => 'bottom',
            ],
            'related_work' => [
                'route' => 'issues.show',
                'routeParameters' => ['project' => $project, 'issue' => $issue],
                'selector' => '[data-tour="issue-related-works"]',
                'placement' => 'bottom',
            ],
        ];

        if (app(CodexConnection::class)->enabled()) {
            $blueprint['codex_pages'] = [
                'route' => 'issues.show',
                'routeParameters' => ['project' => $project, 'issue' => $issue],
                'selector' => '[data-tour="issue-codex-pages"]',
                'placement' => 'bottom',
            ];
        }

        $blueprint['details_tabs'] = [
            'route' => 'issues.show',
            'routeParameters' => ['project' => $project, 'issue' => $issue],
            'selector' => '[data-tour="issue-details-tabs"]',
            'placement' => 'left',
        ];

        $blueprint['attachments'] = [
            'route' => 'issues.show',
            'routeParameters' => ['project' => $project, 'issue' => $issue],
            'selector' => '[data-tour="issue-attachments"]',
            'placement' => 'left',
        ];

        $blueprint['comments'] = [
            'route' => 'issues.show',
            'routeParameters' => ['project' => $project, 'issue' => $issue],
            'selector' => '[data-tour="issue-comments"]',
            'placement' => 'left',
        ];

        $blueprint['followers'] = [
            'route' => 'issues.show',
            'routeParameters' => ['project' => $project, 'issue' => $issue],
            'selector' => '[data-tour="issue-followers"]',
            'placement' => 'left',
        ];

        if ($projectRepo?->supportsVcsLinks()) {
            $blueprint['code_links'] = [
                'route' => 'issues.show',
                'routeParameters' => ['project' => $project, 'issue' => $issue],
                'selector' => '[data-tour="issue-code-links"]',
                'placement' => 'left',
            ];
        }

        foreach ($blueprint as $key => $definition) {
            $steps[] = self::resolveStep(
                $definition,
                "onboarding.tours.issue-detail.steps.{$key}"
            );
        }

        return $steps;
    }

    /**
     * @param  array{route: string, selector: string, placement: string, routeParameters?: array<string, mixed>}  $definition
     * @return array<string, string>
     */
    private static function resolveStep(array $definition, string $translationKey): array
    {
        return [
            'route' => $definition['route'],
            'url' => route($definition['route'], $definition['routeParameters'] ?? []),
            'selector' => $definition['selector'],
            'title' => __($translationKey.'.title'),
            'body' => __($translationKey.'.body'),
            'placement' => $definition['placement'],
        ];
    }
}

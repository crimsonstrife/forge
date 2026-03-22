<?php

namespace App\Livewire\Dashboard;

use App\Models\DashboardPreference;
use App\Services\Dashboards\PersonalDashboardService;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;

final class Overview extends Component
{
    use AuthorizesRequests;

    public string $activeWorkspace = 'overview';

    public string $landingWorkspace = 'overview';

    public bool $showCustomizer = false;

    /** @var array<string, array<int, string>> */
    public array $hiddenWidgets = [];

    /** @var array<string, array<int, string>> */
    public array $widgetOrder = [];

    public function mount(PersonalDashboardService $service): void
    {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        $signals = $service->signals($user);
        $workspaces = $this->workspaceDefinitions($signals);
        $defaultWorkspace = $this->defaultWorkspaceFromSignals($signals, $workspaces);

        $preference = DashboardPreference::query()->firstOrCreate(
            ['user_id' => $user->getKey()],
            [
                'landing_workspace' => $defaultWorkspace,
                'active_workspace' => $defaultWorkspace,
                'hidden_widgets' => [],
                'widget_order' => [],
            ]
        );

        $this->landingWorkspace = $this->validWorkspaceOrFallback(
            (string) $preference->landing_workspace,
            $workspaces,
            $defaultWorkspace
        );
        $this->activeWorkspace = $this->validWorkspaceOrFallback(
            (string) ($preference->active_workspace ?: $this->landingWorkspace),
            $workspaces,
            $this->landingWorkspace
        );
        $this->hiddenWidgets = is_array($preference->hidden_widgets) ? $preference->hidden_widgets : [];
        $this->widgetOrder = is_array($preference->widget_order) ? $preference->widget_order : [];

        if (
            $this->landingWorkspace !== $preference->landing_workspace
            || $this->activeWorkspace !== $preference->active_workspace
        ) {
            $this->persistPreferences();
        }
    }

    public function activateWorkspace(string $workspace): void
    {
        [, $workspaces] = $this->preferenceContext();

        if (! isset($workspaces[$workspace])) {
            return;
        }

        $this->activeWorkspace = $workspace;
        $this->persistPreferences();
    }

    public function makeWorkspaceDefault(?string $workspace = null): void
    {
        [, $workspaces] = $this->preferenceContext();
        $workspace = $workspace ?: $this->activeWorkspace;

        if (! isset($workspaces[$workspace])) {
            return;
        }

        $this->landingWorkspace = $workspace;
        $this->persistPreferences();

        $this->dispatch('notify', title: 'Landing page updated', body: $workspaces[$workspace]['label'].' opens first now.');
    }

    public function toggleWidget(string $widget): void
    {
        [, $workspaces, $catalog] = $this->preferenceContext();
        $workspace = $this->activeWorkspace;

        if (! isset($workspaces[$workspace], $catalog[$widget])) {
            return;
        }

        $availableWidgetIds = array_keys($catalog);
        $ordered = $this->orderedWidgetIds($workspace, $workspaces[$workspace]['default_widgets'], $availableWidgetIds);
        $hidden = $this->hiddenWidgetIds($workspace, $availableWidgetIds);

        if (in_array($widget, $hidden, true)) {
            $hidden = array_values(array_filter($hidden, static fn (string $id): bool => $id !== $widget));
        } else {
            $visibleCount = count(array_values(array_diff($ordered, $hidden)));

            if ($visibleCount <= 1) {
                $this->dispatch('notify', title: 'Keep one widget', body: 'Each workspace needs at least one visible widget.');

                return;
            }

            $hidden[] = $widget;
        }

        $this->hiddenWidgets[$workspace] = array_values(array_unique($hidden));
        $this->persistPreferences();
    }

    public function moveWidgetUp(string $widget): void
    {
        $this->moveWidget($widget, -1);
    }

    public function moveWidgetDown(string $widget): void
    {
        $this->moveWidget($widget, 1);
    }

    public function resetWorkspaceLayout(): void
    {
        unset($this->hiddenWidgets[$this->activeWorkspace], $this->widgetOrder[$this->activeWorkspace]);
        $this->persistPreferences();

        $this->dispatch('notify', title: 'Workspace reset', body: 'The default widget layout has been restored.');
    }

    public function render(PersonalDashboardService $service): View
    {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        $dashboard = $service->build($user);
        $signals = $dashboard['signals'];
        $workspaces = $this->workspaceDefinitions($signals);
        $widgetCatalog = $this->widgetCatalog($signals);
        $defaultWorkspace = $this->defaultWorkspaceFromSignals($signals, $workspaces);

        $this->landingWorkspace = $this->validWorkspaceOrFallback($this->landingWorkspace, $workspaces, $defaultWorkspace);
        $this->activeWorkspace = $this->validWorkspaceOrFallback($this->activeWorkspace, $workspaces, $this->landingWorkspace);

        $orderedWidgetIds = $this->orderedWidgetIds(
            $this->activeWorkspace,
            $workspaces[$this->activeWorkspace]['default_widgets'],
            array_keys($widgetCatalog)
        );
        $hiddenWidgetIds = $this->hiddenWidgetIds($this->activeWorkspace, array_keys($widgetCatalog));
        $visibleWidgetIds = array_values(array_diff($orderedWidgetIds, $hiddenWidgetIds));

        if ($visibleWidgetIds === [] && $orderedWidgetIds !== []) {
            $visibleWidgetIds = [reset($orderedWidgetIds)];
        }

        return view('livewire.dashboard.overview', [
            'activeWorkspaceDefinition' => $workspaces[$this->activeWorkspace],
            'workspaces' => $workspaces,
            'heroStats' => $this->workspaceStats($this->activeWorkspace, $dashboard['widgets']),
            'visibleWidgets' => array_map(
                fn (string $widgetId): array => ['id' => $widgetId] + $widgetCatalog[$widgetId],
                $visibleWidgetIds
            ),
            'customizerWidgets' => array_map(function (string $widgetId) use ($widgetCatalog, $hiddenWidgetIds): array {
                return [
                    'id' => $widgetId,
                    'enabled' => ! in_array($widgetId, $hiddenWidgetIds, true),
                ] + $widgetCatalog[$widgetId];
            }, $orderedWidgetIds),
            'widgetData' => $dashboard['widgets'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $signals
     * @return array<string, array<string, mixed>>
     */
    private function workspaceDefinitions(array $signals): array
    {
        $teamName = data_get($signals, 'team_context.name');

        return collect([
            'overview' => [
                'label' => 'Overview',
                'description' => 'The default cross-project dashboard for assigned work, deadlines, and activity.',
                'default_widgets' => ['my_open_issues', 'due_soon', 'project_portfolio', 'recent_activity'],
            ],
            'my_sprint' => [
                'label' => 'My Sprint',
                'description' => 'Stay inside sprint commitments, due dates, and the issues that need movement this week.',
                'default_widgets' => ['my_sprint', 'my_open_issues', 'due_soon', 'recent_activity'],
                'available' => $signals['has_active_sprint'] || $signals['project_count'] > 0,
            ],
            'team_delivery' => [
                'label' => 'Team Delivery',
                'description' => $teamName
                    ? "Track delivery across {$teamName}, from open work to sprint pressure and overdue items."
                    : 'Track delivery across the projects you can see, with risk and flow surfaced up front.',
                'default_widgets' => ['team_delivery', 'project_portfolio', 'release_health', 'recent_activity'],
                'available' => $signals['project_count'] > 0,
            ],
            'support_queue' => [
                'label' => 'Support Queue',
                'description' => 'Keep open tickets, SLA pressure, and assignment gaps visible without leaving the dashboard.',
                'default_widgets' => ['support_queue', 'my_open_issues', 'recent_activity'],
                'available' => $signals['can_view_support'],
            ],
            'release_health' => [
                'label' => 'Release Health',
                'description' => 'Watch upcoming release windows, open scope, and which milestones need attention.',
                'default_widgets' => ['release_health', 'team_delivery', 'recent_activity'],
                'available' => $signals['has_releases'],
            ],
            'exec_summary' => [
                'label' => 'Exec Summary',
                'description' => 'A higher-level scan of project load, delivery risk, releases, and support demand.',
                'default_widgets' => ['exec_summary', 'release_health', 'support_queue', 'recent_activity'],
                'available' => $signals['project_count'] > 0 || $signals['can_view_support'],
            ],
            'solo_today' => [
                'label' => 'Solo Mode Today',
                'description' => 'A tighter execution view for focus sessions, next work, and just enough surrounding context.',
                'default_widgets' => ['solo_today', 'my_open_issues', 'due_soon'],
            ],
        ])
            ->filter(fn (array $workspace): bool => $workspace['available'] ?? true)
            ->map(fn (array $workspace): array => [
                'label' => $workspace['label'],
                'description' => $workspace['description'],
                'default_widgets' => $workspace['default_widgets'],
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $signals
     * @return array<string, array<string, string>>
     */
    private function widgetCatalog(array $signals): array
    {
        return collect([
            'my_open_issues' => [
                'label' => 'My open issues',
                'description' => 'Assigned work across visible projects.',
                'span' => 'col-xl-6',
                'view' => 'my_open_issues',
            ],
            'due_soon' => [
                'label' => 'Due soon',
                'description' => 'Items due in the next 14 days.',
                'span' => 'col-xl-6',
                'view' => 'due_soon',
            ],
            'project_portfolio' => [
                'label' => 'Projects',
                'description' => 'Your visible projects and current load.',
                'span' => 'col-xl-6',
                'view' => 'project_portfolio',
                'available' => $signals['project_count'] > 0,
            ],
            'recent_activity' => [
                'label' => 'Recent activity',
                'description' => 'Changes across visible work.',
                'span' => 'col-12',
                'view' => 'recent_activity',
            ],
            'my_sprint' => [
                'label' => 'Sprint focus',
                'description' => 'Active sprint summary and assigned sprint issues.',
                'span' => 'col-12',
                'view' => 'my_sprint',
                'available' => $signals['project_count'] > 0,
            ],
            'team_delivery' => [
                'label' => 'Delivery view',
                'description' => 'Project-level delivery and risk metrics.',
                'span' => 'col-12',
                'view' => 'team_delivery',
                'available' => $signals['project_count'] > 0,
            ],
            'support_queue' => [
                'label' => 'Support queue',
                'description' => 'Open tickets, SLAs, and routing.',
                'span' => 'col-12',
                'view' => 'support_queue',
                'available' => $signals['can_view_support'],
            ],
            'release_health' => [
                'label' => 'Release health',
                'description' => 'Release readiness and milestone risk.',
                'span' => 'col-12',
                'view' => 'release_health',
                'available' => $signals['has_releases'],
            ],
            'exec_summary' => [
                'label' => 'Executive summary',
                'description' => 'High-level health and current risks.',
                'span' => 'col-12',
                'view' => 'exec_summary',
                'available' => $signals['project_count'] > 0 || $signals['can_view_support'],
            ],
            'solo_today' => [
                'label' => 'Solo mode today',
                'description' => 'Focus timer, next work, and on-deck issues.',
                'span' => 'col-12',
                'view' => 'solo_today',
            ],
        ])
            ->filter(fn (array $widget): bool => $widget['available'] ?? true)
            ->map(fn (array $widget): array => [
                'label' => $widget['label'],
                'description' => $widget['description'],
                'span' => $widget['span'],
                'view' => $widget['view'],
            ])
            ->all();
    }

    /**
     * @param  array<string, array<string, mixed>>  $workspaces
     */
    private function defaultWorkspaceFromSignals(array $signals, array $workspaces): string
    {
        $preferred = match (true) {
            $signals['can_view_support'] => 'support_queue',
            $signals['is_admin'] => 'exec_summary',
            data_get($signals, 'team_context.is_owner') && ! data_get($signals, 'team_context.is_personal') => 'team_delivery',
            $signals['has_active_sprint'] => 'my_sprint',
            $signals['project_count'] === 0 => 'solo_today',
            default => 'overview',
        };

        return $this->validWorkspaceOrFallback($preferred, $workspaces, 'overview');
    }

    /**
     * @param  array<string, array<string, mixed>>  $workspaces
     */
    private function validWorkspaceOrFallback(string $workspace, array $workspaces, string $fallback): string
    {
        if (isset($workspaces[$workspace])) {
            return $workspace;
        }

        if (isset($workspaces[$fallback])) {
            return $fallback;
        }

        return array_key_first($workspaces) ?? 'overview';
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, array<string, mixed>>, 2: array<string, array<string, string>>}
     */
    private function preferenceContext(): array
    {
        $user = auth()->user();

        abort_unless($user !== null, 403);

        $signals = app(PersonalDashboardService::class)->signals($user);
        $workspaces = $this->workspaceDefinitions($signals);
        $catalog = $this->widgetCatalog($signals);

        return [$signals, $workspaces, $catalog];
    }

    /**
     * @param  array<int, string>  $defaultWidgets
     * @param  array<int, string>  $availableWidgetIds
     * @return array<int, string>
     */
    private function orderedWidgetIds(string $workspace, array $defaultWidgets, array $availableWidgetIds): array
    {
        $savedOrder = $this->filterWidgetIds($this->widgetOrder[$workspace] ?? [], $availableWidgetIds);
        $defaults = $this->filterWidgetIds($defaultWidgets, $availableWidgetIds);

        return array_values(array_unique(array_merge($savedOrder, $defaults, $availableWidgetIds)));
    }

    /**
     * @param  array<int, string>  $availableWidgetIds
     * @return array<int, string>
     */
    private function hiddenWidgetIds(string $workspace, array $availableWidgetIds): array
    {
        return $this->filterWidgetIds($this->hiddenWidgets[$workspace] ?? [], $availableWidgetIds);
    }

    /**
     * @param  array<int, string>  $widgetIds
     * @param  array<int, string>  $availableWidgetIds
     * @return array<int, string>
     */
    private function filterWidgetIds(array $widgetIds, array $availableWidgetIds): array
    {
        return array_values(array_unique(array_filter(
            $widgetIds,
            static fn (string $widgetId): bool => in_array($widgetId, $availableWidgetIds, true)
        )));
    }

    /**
     * @param  array<string, mixed>  $widgetData
     * @return array<int, array<string, int|string|null>>
     */
    private function workspaceStats(string $workspace, array $widgetData): array
    {
        return match ($workspace) {
            'my_sprint' => [
                ['label' => 'Active sprints', 'value' => data_get($widgetData, 'my_sprint.summary.active_sprint_count', 0)],
                ['label' => 'Assigned', 'value' => data_get($widgetData, 'my_sprint.summary.assigned_issue_count', 0)],
                ['label' => 'Due this week', 'value' => data_get($widgetData, 'my_sprint.summary.due_this_week_count', 0)],
            ],
            'team_delivery' => [
                ['label' => 'Projects', 'value' => data_get($widgetData, 'team_delivery.summary.project_count', 0)],
                ['label' => 'Open issues', 'value' => data_get($widgetData, 'team_delivery.summary.open_issue_count', 0)],
                ['label' => 'Overdue', 'value' => data_get($widgetData, 'team_delivery.summary.overdue_count', 0)],
                ['label' => 'Active sprints', 'value' => data_get($widgetData, 'team_delivery.summary.active_sprint_count', 0)],
            ],
            'support_queue' => [
                ['label' => 'Open tickets', 'value' => data_get($widgetData, 'support_queue.summary.open_count', 0)],
                ['label' => 'Breached', 'value' => data_get($widgetData, 'support_queue.summary.breached_count', 0)],
                ['label' => 'Unassigned', 'value' => data_get($widgetData, 'support_queue.summary.unassigned_count', 0)],
                ['label' => 'Mine', 'value' => data_get($widgetData, 'support_queue.summary.mine_count', 0)],
            ],
            'release_health' => [
                ['label' => 'Releases', 'value' => data_get($widgetData, 'release_health.summary.release_count', 0)],
                ['label' => 'At risk', 'value' => data_get($widgetData, 'release_health.summary.at_risk_count', 0)],
                ['label' => 'Next release', 'value' => data_get($widgetData, 'release_health.summary.upcoming_label', 'None')],
            ],
            'exec_summary' => collect(data_get($widgetData, 'exec_summary.stats', []))
                ->take(4)
                ->values()
                ->all(),
            'solo_today' => [
                ['label' => 'Open issues', 'value' => data_get($widgetData, 'solo_today.open_issue_count', 0)],
                ['label' => 'Due soon', 'value' => data_get($widgetData, 'solo_today.due_soon_count', 0)],
            ],
            default => [
                ['label' => 'Open issues', 'value' => count(data_get($widgetData, 'my_open_issues.items', []))],
                ['label' => 'Due soon', 'value' => count(data_get($widgetData, 'due_soon.items', []))],
                ['label' => 'Projects', 'value' => count(data_get($widgetData, 'project_portfolio.items', []))],
            ],
        };
    }

    private function moveWidget(string $widget, int $direction): void
    {
        [, $workspaces, $catalog] = $this->preferenceContext();
        $workspace = $this->activeWorkspace;

        if (! isset($workspaces[$workspace], $catalog[$widget])) {
            return;
        }

        $ordered = $this->orderedWidgetIds($workspace, $workspaces[$workspace]['default_widgets'], array_keys($catalog));
        $index = array_search($widget, $ordered, true);

        if ($index === false) {
            return;
        }

        $targetIndex = $index + $direction;

        if (! isset($ordered[$targetIndex])) {
            return;
        }

        $current = $ordered[$index];
        $ordered[$index] = $ordered[$targetIndex];
        $ordered[$targetIndex] = $current;

        $this->widgetOrder[$workspace] = array_values($ordered);
        $this->persistPreferences();
    }

    private function persistPreferences(): void
    {
        $userId = auth()->id();

        if (! $userId) {
            return;
        }

        DashboardPreference::query()->updateOrCreate(
            ['user_id' => $userId],
            [
                'landing_workspace' => $this->landingWorkspace,
                'active_workspace' => $this->activeWorkspace,
                'hidden_widgets' => $this->hiddenWidgets,
                'widget_order' => $this->widgetOrder,
            ]
        );
    }
}

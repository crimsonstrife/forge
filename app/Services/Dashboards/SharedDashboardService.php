<?php

namespace App\Services\Dashboards;

use App\Enums\GoalStatus;
use App\Models\Activity;
use App\Models\Goal;
use App\Models\Issue;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class SharedDashboardService
{
    /**
     * @return array{
     *     projectCount:int,
     *     openIssuesCount:int,
     *     activeGoalsCount:int,
     *     projects:Collection<int, Project>,
     *     dueSoon:Collection<int, Issue>,
     *     goals:Collection<int, Goal>,
     *     recentActivity:Collection<int, array<string, mixed>>,
     *     members:Collection<int, User>
     * }
     */
    public function forTeam(User $viewer, Team $team): array
    {
        $members = $team->allUsers()
            ->sortBy('name')
            ->values();

        return array_merge(
            $this->build(
                projectQuery: $this->teamProjectQuery($viewer, $team),
                goalOwnerType: Team::class,
                goalOwnerId: (string) $team->getKey(),
            ),
            ['members' => $members],
        );
    }

    /**
     * @return array{
     *     projectCount:int,
     *     openIssuesCount:int,
     *     activeGoalsCount:int,
     *     projects:Collection<int, Project>,
     *     dueSoon:Collection<int, Issue>,
     *     goals:Collection<int, Goal>,
     *     recentActivity:Collection<int, array<string, mixed>>,
     *     teams:Collection<int, Team>
     * }
     */
    public function forOrganization(User $viewer, Organization $organization): array
    {
        $projectQuery = $this->organizationProjectQuery($viewer, $organization);
        $projectIds = (clone $projectQuery)->pluck('projects.id')->values()->all();

        $teams = empty($projectIds)
            ? Team::query()->whereRaw('1 = 0')->get()
            : Team::query()
                ->whereHas('projects', fn (Builder $projects) => $projects->whereIn('projects.id', $projectIds))
                ->withCount([
                    'projects as scoped_projects_count' => fn (Builder $projects) => $projects->whereIn('projects.id', $projectIds),
                ])
                ->orderBy('name')
                ->get(['id', 'name']);

        return array_merge(
            $this->build(
                projectQuery: $projectQuery,
                goalOwnerType: Organization::class,
                goalOwnerId: (string) $organization->getKey(),
            ),
            ['teams' => $teams],
        );
    }

    private function viewerCanSeeAllProjects(User $viewer): bool
    {
        return $viewer->hasPermissionTo('is-super-admin') || $viewer->can('is-admin');
    }

    private function teamProjectQuery(User $viewer, Team $team): Builder
    {
        $query = Project::query()
            ->whereHas('teams', fn (Builder $teams) => $teams->whereKey($team->getKey()));

        if (! $this->viewerCanSeeAllProjects($viewer)) {
            $query->visibleTo($viewer);
        }

        return $query;
    }

    private function organizationProjectQuery(User $viewer, Organization $organization): Builder
    {
        $query = Project::query()
            ->where('organization_id', $organization->getKey());

        if (! $this->viewerCanSeeAllProjects($viewer)) {
            $query->visibleTo($viewer);
        }

        return $query;
    }

    /**
     * @return array{
     *     projectCount:int,
     *     openIssuesCount:int,
     *     activeGoalsCount:int,
     *     projects:Collection<int, Project>,
     *     dueSoon:Collection<int, Issue>,
     *     goals:Collection<int, Goal>,
     *     recentActivity:Collection<int, array<string, mixed>>
     * }
     */
    private function build(Builder $projectQuery, string $goalOwnerType, string $goalOwnerId): array
    {
        $projectIds = (clone $projectQuery)->pluck('projects.id')->values()->all();

        $projects = empty($projectIds)
            ? Project::query()->whereRaw('1 = 0')->get()
            : (clone $projectQuery)
                ->select(['projects.id', 'projects.name', 'projects.key', 'projects.organization_id', 'projects.updated_at'])
                ->with(['organization:id,name,slug', 'teams:id,name'])
                ->withCount([
                    'issues as open_issues_count' => fn (Builder $issues) => $issues->whereHas(
                        'status',
                        fn (Builder $statuses) => $statuses->where('is_done', false)
                    ),
                ])
                ->latest('projects.updated_at')
                ->limit(8)
                ->get();

        $dueSoon = empty($projectIds)
            ? Issue::query()->whereRaw('1 = 0')->get()
            : Issue::query()
                ->select(['id', 'key', 'summary', 'project_id', 'issue_status_id', 'due_at'])
                ->with(['project:id,key,name', 'status:id,name,color,is_done'])
                ->whereIn('project_id', $projectIds)
                ->whereHas('status', fn (Builder $statuses) => $statuses->where('is_done', false))
                ->whereNotNull('due_at')
                ->whereBetween('due_at', [now(), now()->addDays(14)])
                ->orderBy('due_at')
                ->limit(8)
                ->get();

        $goals = Goal::query()
            ->with('owner')
            ->where('owner_type', $goalOwnerType)
            ->where('owner_id', $goalOwnerId)
            ->latest()
            ->limit(6)
            ->get(['id', 'name', 'status', 'progress', 'due_date', 'owner_type', 'owner_id']);

        $openIssuesCount = empty($projectIds)
            ? 0
            : Issue::query()
                ->whereIn('project_id', $projectIds)
                ->whereHas('status', fn (Builder $statuses) => $statuses->where('is_done', false))
                ->count();

        $activeGoalsCount = Goal::query()
            ->where('owner_type', $goalOwnerType)
            ->where('owner_id', $goalOwnerId)
            ->where('status', GoalStatus::Active->value)
            ->count();

        return [
            'projectCount' => count($projectIds),
            'openIssuesCount' => $openIssuesCount,
            'activeGoalsCount' => $activeGoalsCount,
            'projects' => $projects,
            'dueSoon' => $dueSoon,
            'goals' => $goals,
            'recentActivity' => $this->recentActivity($projectIds),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function recentActivity(array $projectIds): Collection
    {
        if ($projectIds === []) {
            return collect();
        }

        /** @var Collection<int, Activity> $raw */
        $raw = Activity::query()
            ->where(function (Builder $query) use ($projectIds) {
                $query
                    ->where(function (Builder $projectActivity) use ($projectIds) {
                        $projectActivity
                            ->where('subject_type', Project::class)
                            ->whereIn('subject_id', $projectIds);
                    })
                    ->orWhereIn('properties->project_id', $projectIds);
            })
            ->latest()
            ->limit(12)
            ->get([
                'id',
                'description',
                'event',
                'created_at',
                'properties',
                'causer_id',
                'causer_type',
                'subject_type',
                'subject_id',
                'log_name',
            ]);

        if ($raw->isEmpty()) {
            return collect();
        }

        $issueIds = $raw
            ->where('subject_type', Issue::class)
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $referencedProjectIds = collect($projectIds)
            ->merge(
                $raw->where('subject_type', Project::class)
                    ->pluck('subject_id')
                    ->filter()
            )
            ->merge(
                $raw->pluck('properties.project_id')
                    ->filter()
            )
            ->unique()
            ->values()
            ->all();

        $causerIds = $raw
            ->where('causer_type', User::class)
            ->pluck('causer_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $projectsById = Project::query()
            ->whereIn('id', $referencedProjectIds)
            ->get(['id', 'key', 'name'])
            ->keyBy('id');

        $issuesById = Issue::query()
            ->whereIn('id', $issueIds)
            ->with(['project:id,key,name'])
            ->get(['id', 'key', 'summary', 'project_id'])
            ->keyBy('id');

        $usersById = User::query()
            ->whereIn('id', $causerIds)
            ->get(['id', 'name', 'profile_photo_path'])
            ->keyBy('id');

        return $raw->map(function (Activity $activity) use ($projectsById, $issuesById, $usersById): array {
            $actor = $activity->causer_type === User::class
                ? $usersById->get($activity->causer_id)
                : null;

            $targetLabel = 'Shared record';
            $targetUrl = null;

            if ($activity->subject_type === Issue::class) {
                /** @var Issue|null $issue */
                $issue = $issuesById->get($activity->subject_id);

                if ($issue && $issue->project) {
                    $targetLabel = "{$issue->key}: {$issue->summary}";
                    $targetUrl = route('issues.show', ['project' => $issue->project, 'issue' => $issue]);
                }
            } elseif ($activity->subject_type === Project::class) {
                /** @var Project|null $project */
                $project = $projectsById->get($activity->subject_id);

                if ($project) {
                    $targetLabel = "{$project->key} — {$project->name}";
                    $targetUrl = route('projects.show', ['project' => $project]);
                }
            } else {
                /** @var Project|null $project */
                $project = $projectsById->get(data_get($activity->properties, 'project_id'));

                if ($project) {
                    $targetLabel = "{$project->key} — {$project->name}";
                    $targetUrl = route('projects.show', ['project' => $project]);
                }
            }

            $verb = $activity->event
                ?: (Str::contains((string) $activity->description, '.')
                    ? Str::after((string) $activity->description, '.')
                    : (string) $activity->description);

            return [
                'id' => $activity->id,
                'actor_name' => $actor?->name ?? 'System',
                'actor_avatar' => $actor?->profile_photo_url ?? $actor?->profile_photo_path ?? null,
                'verb' => (string) Str::of($verb)->replace(['issue.', 'project.', 'comment.'], '')->headline(),
                'target_label' => $targetLabel,
                'target_url' => $targetUrl,
                'ago' => $activity->created_at?->diffForHumans(),
            ];
        });
    }
}

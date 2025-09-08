<?php

namespace App\Livewire\Dashboard;

use App\Models\Activity;
use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

final class Overview extends Component
{
    use AuthorizesRequests;

    /** @var Collection<int,Issue> */
    public Collection $myIssues;

    /** @var array<string,int> */
    public array $statusSummary = [];

    /** @var Collection<int,Issue> */
    public Collection $upcomingDue;

    /** @var Collection<int,Project> */
    public Collection $myProjects;

    /** @var Collection<string, array<int, array<string, mixed>>> */
    public Collection $activityGroups;

    public function mount(): void
    {
        $user = auth()->user();
        $teamId = $user->currentTeam?->id;

        // my issues
        $this->myIssues = Issue::query()
            ->select(['id', 'summary', 'key', 'project_id', 'issue_status_id', 'issue_type_id', 'assignee_id', 'updated_at', 'due_at'])
            ->with([
                'project:id,key,name',
                'status:id,name,color,is_done',
                'type:id,key,name',
            ])
            ->where('assignee_id', $user->id)
            ->whereHas('status', fn ($q) => $q->where('is_done', false))
            ->latest('updated_at')
            ->limit(10)
            ->get();

        // status summary
        $this->statusSummary = Issue::query()
            ->where('assignee_id', $user->id)
            ->selectRaw('issue_status_id, COUNT(*) as total')
            ->groupBy('issue_status_id')
            ->pluck('total', 'issue_status_id')
            ->toArray();

        // due soon
        $this->upcomingDue = Issue::query()
            ->select(['id', 'summary', 'key', 'project_id', 'due_at', 'issue_status_id'])
            ->with(['project:id,key,name', 'status:id,name,color,is_done'])
            ->where('assignee_id', $user->id)
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDays(14)])
            ->orderBy('due_at')
            ->limit(10)
            ->get();

        // my projects
        $this->myProjects = $user->projects()
            ->select(['projects.id', 'projects.name', 'projects.key'])
            ->withCount([
                'issues as open_issues_count' => fn ($q) => $q->whereHas(
                    'status',
                    fn ($s) => $s->where('is_done', false)
                ),
            ])
            ->latest('projects.updated_at')
            ->limit(8)
            ->get();

        /** @var Collection<int, Activity> $rawActivity */
        $rawActivity = Activity::query()
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
            ->latest()
            ->limit(50)
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
                'team_id',
            ]);

        // Collect IDs to avoid N+1s
        $issueIds   = $rawActivity->where('subject_type', Issue::class)->pluck('subject_id')->filter()->unique();
        $projectIds = $rawActivity->where('subject_type', Project::class)->pluck('subject_id')->filter()->unique();
        $causerIds  = $rawActivity->where('causer_type', User::class)->pluck('causer_id')->filter()->unique();

        // Scan changed fields to preload related labels
        $statusIds = $assigneeIds = $priorityIds = $typeIds = $parentIds = [];

        foreach ($rawActivity as $a) {
            /** @var array<string,mixed> $props */
            $props = (array)($a->properties ?? []);
            $new   = (array)($props['attributes'] ?? $props['new'] ?? []);
            $old   = (array)($props['old'] ?? $props['attributes_before'] ?? []);

            foreach (['issue_status_id', 'assignee_id', 'issue_priority_id', 'issue_type_id', 'parent_id'] as $k) {
                if (isset($new[$k])) {
                    ${Str::of($k)->before('_id')->append('Ids')}[] = $new[$k];
                }
                if (isset($old[$k])) {
                    ${Str::of($k)->before('_id')->append('Ids')}[] = $old[$k];
                }
            }

            // Also harvest project_id from properties (so we can link)
            if (!empty($props['project_id'])) {
                $projectIds->push($props['project_id']);
            }
        }

        $issuesById = Issue::query()
            ->whereIn('id', array_filter($issueIds->all()))
            ->with(['project:id,key,name'])
            ->get(['id', 'key', 'summary', 'project_id'])
            ->keyBy('id');

        $usersById = User::query()
            ->whereIn('id', array_filter($causerIds->all()))
            ->get(['id', 'name', 'profile_photo_path'])
            ->keyBy('id');

        $projectsById = Project::query()
            ->whereIn('id', array_filter($projectIds->all()))
            ->get(['id', 'key', 'name'])
            ->keyBy('id');

        $statusMap   = IssueStatus::query()->whereIn('id', array_filter($statusIds))->get(['id', 'name', 'color'])->keyBy('id');
        $assigneeMap = User::query()->whereIn('id', array_filter($assigneeIds))->get(['id', 'name', 'profile_photo_path'])->keyBy('id');
        $priorityMap = IssuePriority::query()->whereIn('id', array_filter($priorityIds))->get(['id', 'name'])->keyBy('id');
        $typeMap     = IssueType::query()->whereIn('id', array_filter($typeIds))->get(['id', 'name'])->keyBy('id');
        $parentMap   = Issue::query()->whereIn('id', array_filter($parentIds))->get(['id', 'key', 'summary'])->keyBy('id');

        $labelMap = [
            'summary'           => 'Summary',
            'description'       => 'Description',
            'issue_status_id'   => 'Status',
            'assignee_id'       => 'Assignee',
            'issue_priority_id' => 'Priority',
            'issue_type_id'     => 'Type',
            'parent_id'         => 'Parent',
        ];

        $enriched = $rawActivity->map(function (Activity $a) use (
            $usersById,
            $issuesById,
            $projectsById,
            $labelMap,
            $statusMap,
            $assigneeMap,
            $priorityMap,
            $typeMap,
            $parentMap
        ) {
            /** @var array<string,mixed> $props */
            $props = (array)($a->properties ?? []);
            $new   = (array)($props['attributes'] ?? $props['new'] ?? []);
            $old   = (array)($props['old'] ?? $props['attributes_before'] ?? []);

            $actor = $a->causer_type === User::class ? $usersById->get($a->causer_id) : null;
            $actorName   = $actor?->name ?? 'System';
            $actorAvatar = $actor?->profile_photo_url ?? $actor?->profile_photo_path ?? null;

            // Target + link resolution (dashboard-wide)
            $targetType  = $a->subject_type === Issue::class ? 'issue'
                : ($a->subject_type === Project::class ? 'project' : ($a->log_name ?: 'record'));
            $targetLabel = 'record';
            $targetUrl   = null;

            if ($a->subject_type === Issue::class) {
                $issue = $issuesById->get($a->subject_id);
                if ($issue) {
                    $targetLabel = "{$issue->key}: {$issue->summary}";
                    $project = $issue->getRelation('project') ?: ($projectsById->get($issue->project_id));
                    if ($project) {
                        $targetUrl = route('issues.show', ['project' => $project, 'issue' => $issue]);
                    }
                }
            } elseif ($a->subject_type === Project::class) {
                $project = $projectsById->get($a->subject_id);
                if ($project) {
                    $targetLabel = "{$project->key} — {$project->name}";
                    $targetUrl   = route('projects.show', ['project' => $project]);
                }
            } else {
                if (isset($props['issue_key'], $props['issue_summary'])) {
                    $targetLabel = "{$props['issue_key']}: {$props['issue_summary']}";
                    if (!empty($props['project_id']) && ($p = $projectsById->get($props['project_id']))) {
                        $targetUrl = route('issues.index', ['project' => $p, 'search' => $props['issue_key']]);
                    }
                }
            }

            // Verb
            $verb = $a->event ?: (Str::contains((string)$a->description, '.') ? Str::after((string)$a->description, '.') : (string)$a->description);
            $verb = Str::of($verb)->replace(['issue.', 'project.'], '')->headline();

            // Field diffs
            $changes = [];
            $keys = array_unique(array_merge(array_keys($new), array_keys($old)));
            foreach ($keys as $k) {
                $label = $labelMap[$k] ?? Str::of($k)->headline()->toString();
                $from  = $old[$k] ?? null;
                $to    = $new[$k] ?? null;

                if ($k === 'issue_status_id') {
                    $from = $from ? ($statusMap->get($from)?->name ?? $from) : null;
                    $to   = $to ? ($statusMap->get($to)?->name ?? $to) : null;
                } elseif ($k === 'assignee_id') {
                    $from = $from ? ($assigneeMap->get($from)?->name ?? $from) : null;
                    $to   = $to ? ($assigneeMap->get($to)?->name ?? $to) : null;
                } elseif ($k === 'issue_priority_id') {
                    $from = $from ? ($priorityMap->get($from)?->name ?? $from) : null;
                    $to   = $to ? ($priorityMap->get($to)?->name ?? $to) : null;
                } elseif ($k === 'issue_type_id') {
                    $from = $from ? ($typeMap->get($from)?->name ?? $from) : null;
                    $to   = $to ? ($typeMap->get($to)?->name ?? $to) : null;
                } elseif ($k === 'parent_id') {
                    $from = $from ? (($p = $parentMap->get($from)) ? "{$p->key}: {$p->summary}" : $from) : null;
                    $to   = $to ? (($p = $parentMap->get($to)) ? "{$p->key}: {$p->summary}" : $to) : null;
                }

                if (($from ?? '') === ($to ?? '')) {
                    continue;
                }

                $changes[] = [
                    'label'    => $label,
                    'from'     => $from,
                    'to'       => $to,
                    'key'      => $k,
                    'to_color' => $k === 'issue_status_id' && isset($new['issue_status_id'])
                        ? ($statusMap->get($new['issue_status_id'])->color ?? null)
                        : null,
                ];
            }

            return [
                'id'          => $a->id,
                'actor_name'  => $actorName,
                'actor_avatar' => $actorAvatar,
                'verb'        => (string)$verb,
                'target_type' => $targetType,
                'target_label' => $targetLabel,
                'target_url'  => $targetUrl,
                'changes'     => $changes,
                'created_at'  => $a->created_at,
                'ago'         => $a->created_at?->diffForHumans(),
            ];
        });

        $this->activityGroups = $enriched->groupBy(function (array $i) {
            $dt = $i['created_at'];
            if ($dt?->isToday()) {
                return 'Today';
            }
            if ($dt?->isYesterday()) {
                return 'Yesterday';
            }
            return $dt?->toFormattedDateString() ?? 'Recent';
        });
    }

    /**
     * @param Collection<int,mixed>|array<int,mixed> $ids
     * @return array<int,int>
     */
    private function idsList(Collection|array $ids): array
    {
        $arr = $ids instanceof Collection ? $ids->all() : $ids;
        $arr = \Illuminate\Support\Arr::flatten($arr);
        $arr = array_map('intval', $arr);
        return array_values(array_unique(array_filter($arr)));
    }

    public function render(): View
    {
        return view('livewire.dashboard.overview');
    }
}

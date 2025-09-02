<?php

namespace App\Livewire\Dashboard;

use App\Models\Activity;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Collection;
use Livewire\Component;

final class Overview extends Component
{
    use AuthorizesRequests;

    /** @var Collection<int,Issue> */
    public Collection $myIssues;

    /** @var array<string,int> map status_id => total */
    public array $statusSummary = [];

    /** @var Collection<int,Issue> */
    public Collection $upcomingDue;

    /** @var Collection<int,Project> */
    public Collection $myProjects;

    /** @var Collection<int,Activity> */
    public Collection $recentActivity;

    public function mount(): void
    {
        $user = auth()->user();
        $teamId = $user->currentTeam?->id;

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

        $this->statusSummary = Issue::query()
            ->where('assignee_id', $user->id)
            ->selectRaw('issue_status_id, COUNT(*) as total')
            ->groupBy('issue_status_id')
            ->pluck('total', 'issue_status_id')
            ->toArray();

        $this->upcomingDue = Issue::query()
            ->select(['id', 'summary', 'key', 'project_id', 'due_at', 'issue_status_id'])
            ->with(['project:id,key,name', 'status:id,name,color,is_done'])
            ->where('assignee_id', $user->id)
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [now(), now()->addDays(14)])
            ->orderBy('due_at')
            ->limit(10)
            ->get();

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

        $this->recentActivity = Activity::query()
            ->when($teamId, fn ($q) => $q->where('team_id', $teamId))
            ->latest('created_at')
            ->limit(15)
            ->with([
                'causer:id,name', // ⬅️ replaces "actor"
                'subject' => fn (MorphTo $morphTo) => $morphTo->morphWith([
                    Issue::class   => ['project:id,key,name'],
                    Project::class => [], // subject might already be a Project
                ]),
            ])
            ->get([
                'id', 'team_id', 'log_name', 'description',
                'causer_id', 'subject_type', 'subject_id', 'created_at',
            ]);
    }

    public function render(): View
    {
        return view('livewire.dashboard.overview');
    }
}

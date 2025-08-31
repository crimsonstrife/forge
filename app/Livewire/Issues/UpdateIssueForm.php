<?php

namespace App\Livewire\Issues;

use App\Models\Issue;
use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class UpdateIssueForm extends Component
{
    use AuthorizesRequests;

    public Project $project;
    public Issue $issue;

    /** @var array<int, array{id:string,name:string}> */
    public array $typeOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $statusOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $priorityOptions = [];

    /** @var array<int, array{id:string,name:string}> */
    public array $assigneeOptions = [];

    #[Validate(['summary' => 'required|string|min:3|max:200'])]
    public string $summary = '';

    #[Validate(['description' => 'nullable|string|max:5000'])]
    public ?string $description = null;

    #[Validate(['issue_type_id' => 'required'])]
    public string $issue_type_id = '';

    #[Validate(['issue_status_id' => 'required'])]
    public string $issue_status_id = '';

    #[Validate(['issue_priority_id' => 'nullable'])]
    public ?string $issue_priority_id = null;

    #[Validate(['assignee_id' => 'nullable|uuid'])]
    public ?string $assignee_id = null;

    #[Validate(['story_points' => 'nullable|integer|min:0|max:999'])]
    public $story_points = null;

    #[Validate(['estimate_minutes' => 'nullable|integer|min:0|max:100000'])]
    public $estimate_minutes = null;

    /** Comma-separated tags input */
    public string $tags = '';

    /** HTML5 datetime-local strings (Y-m-d\TH:i) */
    #[Validate(['starts_at_input' => 'nullable|date_format:Y-m-d\TH:i'])]
    public ?string $starts_at_input = null;

    #[Validate(['due_at_input' => 'nullable|date_format:Y-m-d\TH:i'])]
    public ?string $due_at_input = null;


    public function mount(Project $project, Issue $issue): void
    {
        $this->authorize('update', $issue);

        $this->project = $project;
        $this->issue = $issue;

        // Options
        $this->typeOptions = IssueType::query()
            ->select('id','name')->orderBy('name')->get()->toArray();

        $this->statusOptions = \App\Models\IssueStatus::query()
            ->select('issue_statuses.id', 'issue_statuses.name')
            ->whereHas('projects', fn ($q) => $q->whereKey($project->id))
            ->orderBy('name')
            ->get()
            ->toArray();

        if (empty($this->statusOptions)) {
            $this->statusOptions = \App\Models\IssueStatus::query()
                ->select('id','name')->orderBy('name')->get()->toArray();
        }

        $this->priorityOptions = IssuePriority::query()
            ->select('id','name')->orderBy('name')->get()->toArray();

        // You may want to scope to project members; for now list active users.
        $this->assigneeOptions = User::query()
            ->select('id','name')->orderBy('name')->limit(200)->get()->toArray();

        // Seed fields
        $this->summary          = (string) $issue->summary;
        $this->description      = $issue->description;
        $this->issue_type_id    = (string) $issue->issue_type_id;
        $this->issue_status_id  = (string) $issue->issue_status_id;
        $this->issue_priority_id= $issue->issue_priority_id ?: null;
        $this->assignee_id      = $issue->assignee_id ?: null;
        $this->story_points     = $issue->story_points;
        $this->estimate_minutes = $issue->estimate_minutes;
        $this->tags             = $issue->tags->pluck('name')->implode(', ');

        $tz = auth()->user()->timezone ?? config('app.timezone', 'UTC');

        $this->starts_at_input = $issue->starts_at
            ? $issue->starts_at->setTimezone($tz)->format('Y-m-d\TH:i')
            : null;

        $this->due_at_input = $issue->due_at
            ? $issue->due_at->setTimezone($tz)->format('Y-m-d\TH:i')
            : null;
    }

    public function save(): void
    {
        $this->authorize('update', $this->issue);
        $this->validate([
            'starts_at_input' => 'nullable|date_format:Y-m-d\TH:i',
            'due_at_input'    => 'nullable|date_format:Y-m-d\TH:i',
        ]);

        $tz = auth()->user()->timezone ?? config('app.timezone', 'UTC');

        $start = $this->starts_at_input
            ? CarbonImmutable::createFromFormat('Y-m-d\TH:i', $this->starts_at_input, $tz)
            : null;

        $due = $this->due_at_input
            ? CarbonImmutable::createFromFormat('Y-m-d\TH:i', $this->due_at_input, $tz)
            : null;

        if ($start && $due && $due->lessThan($start)) {
            $this->addError('due_at_input', 'Due date/time must be after or equal to Start.');
            return;
        }

        $this->issue->fill([
            'summary'           => $this->summary,
            'description'       => $this->description,
            'issue_type_id'     => $this->issue_type_id,
            'issue_status_id'   => $this->issue_status_id,
            'issue_priority_id' => $this->issue_priority_id,
            'assignee_id'       => $this->assignee_id,
            'story_points'      => $this->story_points,
            'estimate_minutes'  => $this->estimate_minutes,
            'starts_at'         => $start,
            'due_at'            => $due,
        ]);

        $this->issue->save();

        // Tags
        $tags = collect(explode(',', $this->tags))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->values()
            ->all();
        $this->issue->syncTags($tags);

        $this->dispatch('notify', title: 'Issue updated');
        $this->redirect(route('issues.show', ['project' => $this->project, 'issue' => $this->issue]), navigate: true);
    }

    public function clearStart(): void
    {
        $this->starts_at_input = null;
    }

    public function clearDue(): void
    {
        $this->due_at_input = null;
    }

    public function render(): View
    {
        return view('livewire.issues.update-issue-form');
    }
}

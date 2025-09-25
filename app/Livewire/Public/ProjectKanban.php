<?php

namespace App\Livewire\Public;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.blank')] // minimal chrome for embed; use your blank layout
class ProjectKanban extends Component
{
    public Project $project;

    #[Url(as: 'q')]
    public ?string $search = null;

    #[Url]
    public array $status = []; // selected status IDs

    #[Url]
    public array $type = [];   // selected type IDs (if you have IssueType)

    #[Url]
    public ?string $assignee = null;

    #[Url]
    public bool $compact = false;

    /** @var array<int, array{id:string,title:string,status_id:string|null,type_id:string|null,assignee?:array{ id:string,name:string},tags?:array<string>}> */
    public array $columns = [];

    public int $progress = 0;  // 0..100
    public int $totalCount = 0;
    public int $doneCount = 0;

    public function mount(Project $project): void
    {
        abort_unless($project->isPubliclyEmbeddable(), 404);
        $this->project = $project;
    }

    public function render(): View
    {
        // Build base queries
        $base = Issue::query()
            ->with(['status', 'type', 'assignee', 'tags'])
            ->where('project_id', $this->project->id);

        // Visible (public) set for display
        $visible = (clone $base)->publicVisible();
        $visible = $this->applyFilters($visible)->get();

        // Columns by status
        /** @var Collection<int, IssueStatus> $statuses */
        $statuses = IssueStatus::query()
            ->where(function ($q) {
                $q->whereNull('project_id')->orWhere('project_id', $this->project->id);
            })
            ->orderBy('sort_order')
            ->get();

        $this->columns = $statuses->map(function (IssueStatus $s) use ($visible) {
            $items = $visible->where('status_id', $s->id)->map(fn (Issue $i) => [
                'id' => (string) $i->id,
                'title' => $i->title,
                'status_id' => (string) $i->status_id,
                'type_id' => $i->type_id ? (string) $i->type_id : null,
                'assignee' => $i->assignee ? ['id' => (string) $i->assignee->id, 'name' => $i->assignee->name] : null,
                'tags' => $i->tags?->pluck('name')->all() ?? [],
            ])->values()->all();

            return [
                'status' => ['id' => (string) $s->id, 'name' => $s->name, 'is_done' => (bool) $s->is_done],
                'items' => $items,
            ];
        })->values()->all();

        // Progress counters (optionally include private issues)
        $countSource = $this->project->count_private_in_progress ? $base : (clone $base)->publicVisible();

        $this->totalCount = (clone $countSource)->count();
        $this->doneCount = (clone $countSource)
            ->whereHas('status', static fn ($q) => $q->where('is_done', true))
            ->count();

        $this->progress = $this->totalCount > 0 ? (int) round(($this->doneCount / $this->totalCount) * 100) : 0;

        return view('livewire.public.project-kanban');
    }

    private function applyFilters(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        if (filled($this->search)) {
            $query->where(static function ($q): void {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('key', 'like', '%' . $this->search . '%');
            });
        }

        if (! empty($this->status)) {
            $query->whereIn('status_id', $this->status);
        }

        if (! empty($this->type)) {
            $query->whereIn('type_id', $this->type);
        }

        if (filled($this->assignee)) {
            $query->where('assignee_id', $this->assignee);
        }

        return $query;
    }
}

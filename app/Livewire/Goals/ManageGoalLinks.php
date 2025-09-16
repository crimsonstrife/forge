<?php

namespace App\Livewire\Goals;

use App\Models\Goal;
use App\Models\GoalLink;
use App\Models\Issue;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Team;
use App\Services\GoalProgressService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Manage polymorphic links for a Goal.
 *
 * @property-read array<int, array{ id:string, type:string, label:string, sublabel:string }>
 */
final class ManageGoalLinks extends Component
{
    use AuthorizesRequests;

    public Goal $goal;

    public string $q = '';

    /** @var array<int,string> */
    public array $types = ['project','issue'];

    /** @var array<int, array{id:string,type:string,label:string,sublabel:string}> */
    public array $results = [];

    /** @var array<string,int> goal_link_id => weight */
    public array $weights = [];

    public function mount(Goal $goal): void
    {
        $this->authorize('update', $goal);

        $this->goal = $goal->loadMissing(['links.linkable']);

        $this->weights = $goal->links
            ->mapWithKeys(fn (GoalLink $l) => [(string) $l->getKey() => (int) $l->weight])
            ->all();

        if ($this->goal->owner_type === Project::class) {
            $this->types = ['issue'];
        }
    }

    public function updatedQ(): void
    {
        $this->search();
    }

    public function updatedTypes(): void
    {
        if ($this->goal->owner_type === Project::class) {
            $this->types = ['issue'];
        }
        $this->search();
    }

    public function search(): void
    {
        $q = trim($this->q);
        $wantProjects = in_array('project', $this->types, true);
        $wantIssues   = in_array('issue', $this->types, true);

        $out = [];

        // prevent suggesting things already linked
        $existing = $this->goal->links
            ->map(fn (GoalLink $l) => $l->linkable_type . ':' . $l->linkable_id)
            ->all();

        if ($wantProjects) {
            /** @var EloquentCollection<Project> $projects */
            $projects = Project::query()
                ->tap(fn (Builder $b) => $this->restrictProjectQuery($b))   // 👈 scope to owner
                ->when($q !== '', fn ($qq) => $qq->where('name', 'like', "%{$q}%"))
                ->orderBy('name')
                ->limit(10)
                ->get(['id','name']);

            foreach ($projects as $p) {
                $key = Project::class . ':' . (string) $p->id;
                if (in_array($key, $existing, true)) {
                    continue;
                }

                $out[] = [
                    'id' => (string) $p->id,
                    'type' => 'project',
                    'label' => $p->name,
                    'sublabel' => 'Project',
                ];
            }
        }

        if ($wantIssues) {
            /** @var EloquentCollection<Issue> $issues */
            $issues = Issue::query()
                ->tap(fn (Builder $b) => $this->restrictIssueQuery($b))     // 👈 scope to owner
                ->when($q !== '', function ($qq) use ($q) {
                    $qq->where(function ($w) use ($q) {
                        $w->where('key', 'like', "%{$q}%")
                            ->orWhere('summary', 'like', "%{$q}%");
                    });
                })
                ->latest()
                ->limit(10)
                ->get(['id','key','summary','project_id']);

            foreach ($issues as $i) {
                $key = Issue::class . ':' . (string) $i->id;
                if (in_array($key, $existing, true)) {
                    continue;
                }

                $out[] = [
                    'id' => (string) $i->id,
                    'type' => 'issue',
                    'label' => "[{$i->key}] {$i->summary}",
                    'sublabel' => 'Issue',
                ];
            }
        }

        $this->results = $out;
    }

    public function add(string $type, string $id): void
    {
        $this->authorize('update', $this->goal);

        $model = match ($type) {
            'project' => Project::query()->findOrFail($id),
            'issue'   => Issue::query()->with('project:id')->findOrFail($id),
            default   => abort(400, 'Unsupported link type.'),
        };

        // enforce ownership policy (prevents crafted requests)
        $this->assertCanLink($type, $model);

        $link = $this->goal->linkTo($model);
        $this->weights[(string) $link->getKey()] = (int) $link->weight;

        $this->goal = $this->goal->fresh(['links.linkable']);

        app(GoalProgressService::class)->recalcGoal($this->goal);

        $this->reset('q', 'results');
        $this->dispatch('toast', body: 'Linked successfully.');
    }

    public function remove(string $goalLinkId): void
    {
        $this->authorize('update', $this->goal);

        $deleted = $this->goal->links()->whereKey($goalLinkId)->delete();
        if ($deleted) {
            unset($this->weights[$goalLinkId]);

            $this->goal = $this->goal->fresh(['links.linkable']);

            // Recompute goal progress now that links changed
            app(GoalProgressService::class)->recalcGoal($this->goal);

            $this->dispatch('toast', body: 'Link removed.');
        }
    }

    public function saveWeights(): void
    {
        $this->authorize('update', $this->goal);

        foreach ($this->weights as $linkId => $weight) {
            $this->goal->links()->whereKey($linkId)->update(['weight' => (int) $weight]);
        }

        $this->goal = $this->goal->fresh(['links.linkable']);

        $this->dispatch('toast', body: 'Weights saved.');
    }

    private function restrictProjectQuery(Builder $q): Builder
    {
        if ($this->goal->owner_type === Project::class) {
            return $q->whereRaw('1 = 0');
        }

        if ($this->goal->owner_type === Team::class) {
            return $q->whereHas('teams', fn (Builder $t) => $t->whereKey($this->goal->owner_id));
        }

        if ($this->goal->owner_type === Organization::class) {
            return $q->where('organization_id', $this->goal->owner_id);
        }

        return $q;
    }

    private function restrictIssueQuery(Builder $q): Builder
    {
        if ($this->goal->owner_type === Project::class) {
            return $q->where('project_id', $this->goal->owner_id);
        }

        if ($this->goal->owner_type === Team::class) {
            return $q->whereHas('project.teams', fn (Builder $t) => $t->whereKey($this->goal->owner_id));
        }

        if ($this->goal->owner_type === Organization::class) {
            return $q->whereHas('project', fn (Builder $p) => $p->where('organization_id', $this->goal->owner_id));
        }

        return $q;
    }

    /**
     * @param 'project'|'issue' $type
     * @param object $model
     */
    private function assertCanLink(string $type, object $model): void
    {
        if ($this->goal->owner_type === Project::class) {
            if ($type === 'project') {
                abort(403, 'Cannot link projects to a project-owned goal.');
            }
            if ($type === 'issue' && $model instanceof Issue) {
                if ((string) $model->project_id !== (string) $this->goal->owner_id) {
                    abort(403, 'Issue must belong to the owning project.');
                }
            }
            return;
        }

        if ($this->goal->owner_type === Team::class) {
            if ($type === 'project' && $model instanceof Project) {
                if (!$model->teams()->whereKey($this->goal->owner_id)->exists()) {
                    abort(403, 'Project is not attached to the owning team.');
                }
            }

            if ($type === 'issue' && $model instanceof Issue) {
                $model->loadMissing('project.teams');
                if (!$model->project?->teams()->whereKey($this->goal->owner_id)->exists()) {
                    abort(403, 'Issue’s project is not attached to the owning team.');
                }
            }
            return;
        }

        if ($this->goal->owner_type === Organization::class) {
            if ($type === 'project' && $model instanceof Project) {
                if ((string) $model->organization_id !== (string) $this->goal->owner_id) {
                    abort(403, 'Project is not in the owning organization.');
                }
            }

            if ($type === 'issue' && $model instanceof Issue) {
                $model->loadMissing('project');
                if ((string) ($model->project->organization_id ?? '') !== (string) $this->goal->owner_id) {
                    abort(403, 'Issue’s project is not in the owning organization.');
                }
            }
        }
    }

    public function render(): View
    {
        return view('livewire.goals.manage-goal-links');
    }
}

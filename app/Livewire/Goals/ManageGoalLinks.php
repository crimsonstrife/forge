<?php
namespace App\Livewire\Goals;

use App\Models\Goal;
use App\Models\GoalLink;
use App\Models\Issue;
use App\Models\Project;
use App\Services\GoalProgressService;
use Illuminate\Contracts\View\View;
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
            ->mapWithKeys(fn(GoalLink $l) => [(string) $l->getKey() => (int) $l->weight])
            ->all();
    }

    public function updatedQ(): void
    {
        $this->search();
    }

    public function updatedTypes(): void
    {
        $this->search();
    }

    public function search(): void
    {
        $q = trim($this->q);
        $wantProjects = in_array('project', $this->types, true);
        $wantIssues   = in_array('issue', $this->types, true);

        $out = [];

        if ($wantProjects) {
            /** @var EloquentCollection<Project> $projects */
            $projects = Project::query()
                ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
                ->orderBy('name')
                ->limit(10)
                ->get(['id','name']);

            foreach ($projects as $p) {
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
                ->when($q !== '', function ($qq) use ($q) {
                    $qq->where('key', 'like', "%{$q}%")
                        ->orWhere('summary', 'like', "%{$q}%");
                })
                ->latest()
                ->limit(10)
                ->get(['id','key','summary']);

            foreach ($issues as $i) {
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
            'issue'   => Issue::query()->findOrFail($id),
            default   => abort(400, 'Unsupported link type.'),
        };

        $link = $this->goal->linkTo($model);
        $linkId = (string) $link->getKey();
        $this->weights[$linkId] = (int) $link->weight;

        $this->goal = $this->goal->fresh(['links.linkable']);

        // Recompute goal progress now that links changed
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

    public function render(): View
    {
        return view('livewire.goals.manage-goal-links');
    }
}

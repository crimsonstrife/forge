<?php

namespace App\Livewire\Projects;

use App\Models\FeedbackBoard;
use App\Models\FeedbackPost;
use App\Models\Project;
use App\Models\ServiceProduct;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use Spatie\Permission\PermissionRegistrar;

class ProjectFeedbackPosts extends Component
{
    use WithPagination;

    public Project $project;

    public ?string $boardId = null;

    public ?string $selectedPostId = null;

    public string $status = '';

    public string $category = '';

    public string $search = '';

    public string $sort = 'top';

    protected string $paginationTheme = 'bootstrap';

    public function mount(Project $project): void
    {
        /** @var User|null $user */
        $user = auth()->user();
        $teamId = $user?->current_team_id;
        if ($teamId !== null) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);
        }

        $this->authorizeProjectView($user, $project);

        $this->project = $project;
        $this->boardId = $this->boards()->first()?->getKey();
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['boardId', 'status', 'category', 'search', 'sort'], true)) {
            $this->selectedPostId = null;
            $this->resetPage();
        }
    }

    public function selectPost(string $postId): void
    {
        $this->selectedPostId = $postId;
    }

    /** @return Collection<int, FeedbackBoard> */
    public function boards(): Collection
    {
        $productIds = ServiceProduct::query()
            ->where('default_project_id', $this->project->getKey())
            ->orWhere('auto_create_issue_project_id', $this->project->getKey())
            ->orWhereHas('projects', fn (Builder $query) => $query->whereKey($this->project->getKey()))
            ->pluck('id');

        if ($productIds->isEmpty()) {
            return collect();
        }

        return FeedbackBoard::query()
            ->with(['product:id,name', 'statuses', 'categories'])
            ->withCount('posts')
            ->whereIn('service_product_id', $productIds)
            ->orderBy('name')
            ->get();
    }

    public function selectedPost(): ?FeedbackPost
    {
        if ($this->selectedPostId === null) {
            return null;
        }

        return FeedbackPost::query()
            ->with([
                'board.product:id,name',
                'status',
                'category',
                'identity',
                'issues:id,key,summary',
                'comments' => fn ($query) => $query
                    ->whereTopLevel()
                    ->with(['identity', 'staffUser', 'replies.identity', 'replies.staffUser'])
                    ->oldest(),
            ])
            ->whereIn('board_id', $this->boards()->pluck('id'))
            ->find($this->selectedPostId);
    }

    public function render(): View
    {
        $boards = $this->boards();
        $board = $boards->firstWhere('id', $this->boardId) ?? $boards->first();

        if ($board !== null && $this->boardId !== $board->getKey()) {
            $this->boardId = $board->getKey();
        }

        $posts = FeedbackPost::query()
            ->with(['status', 'category', 'identity'])
            ->withCount('comments')
            ->when($board, fn (Builder $query) => $query->where('board_id', $board->getKey()))
            ->when($board === null, fn (Builder $query) => $query->whereRaw('1 = 0'))
            ->when($this->status !== '', fn (Builder $query) => $query->whereHas('status', fn (Builder $status) => $status->where('slug', $this->status)))
            ->when($this->category !== '', fn (Builder $query) => $query->whereHas('category', fn (Builder $category) => $category->where('slug', $this->category)))
            ->when($this->search !== '', fn (Builder $query) => $query->where(function (Builder $search): void {
                $search->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('key', 'like', '%'.$this->search.'%');
            }))
            ->when($this->sort === 'new', fn (Builder $query) => $query->latest())
            ->when($this->sort === 'trending', fn (Builder $query) => $query->orderByDesc('trending_score'))
            ->when(! in_array($this->sort, ['new', 'trending'], true), fn (Builder $query) => $query
                ->orderByDesc('is_pinned')
                ->orderByDesc('net_score')
                ->latest('last_activity_at'))
            ->paginate(15);

        return view('livewire.projects.project-feedback-posts', [
            'boards' => $boards,
            'board' => $board,
            'posts' => $posts,
            'selectedPost' => $this->selectedPost(),
        ]);
    }

    private function authorizeProjectView(?User $user, Project $project): void
    {
        abort_unless($user instanceof User, 403);

        $response = Gate::forUser($user)->inspect('view', $project);
        if ($response->allowed() || $this->hasProjectAccess($user, $project)) {
            return;
        }

        $response->authorize();
    }

    private function hasProjectAccess(User $user, Project $project): bool
    {
        try {
            if ($user->hasPermissionTo('projects.view', 'web') && $project->isAccessibleBy($user)) {
                return true;
            }
        } catch (PermissionDoesNotExist) {
        }

        return $project->isAccessibleBy($user);
    }
}

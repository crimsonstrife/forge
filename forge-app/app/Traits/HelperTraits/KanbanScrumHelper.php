<?php

namespace App\Traits\HelperTraits;

use App\Models\User;
use App\Models\Project;
use App\Models\Issues\Issue;
use App\Models\Issues\IssueType;
use App\Models\Issues\IssueStatus;
use App\Models\Issues\IssuePriority;
use App\Models\Board;
use Filament\Facades\Filament;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

/**
 * Provides functions to help organize and manage Kanban and Scrum boards.
 * @package App\Traits\HelperTraits
 */
trait KanbanScrumHelper
{
    // Public Variables
    public bool $isSortable = true;
    public Project|null $project = null;
    public $users = [];
    public $types = [];
    public $priorities = [];
    public $statuses = [];
    public $issues = [];
    public Board|null $board = null;
    public bool $includeNotAffectedIssues = false;
    public bool $issue = false;

    /**
     * Make a Schema array
     * @return array Returns a Schema array.
     */
    protected function formSchema(): array
    {
        return [
            Grid::make([
                'default' => 2,
                'md' => 6
            ])
                ->schema([
                    Select::make('users')
                        ->label('Owners / Assignees')
                        ->multiple()
                        ->options(User::all()->pluck('name', 'id')->toArray())
                        ->on('change', fn() => $this->emit('userSelected', $this->user_id)),
                    Select::make('types')
                        ->label('Issue Types')
                        ->multiple()
                        ->options(IssueType::all()->pluck('name', 'id')->toArray())
                        ->on('change', fn() => $this->emit('typeSelected', $this->type_id)),
                    Select::make('priorities')
                        ->label('Issue Priorities')
                        ->multiple()
                        ->options(IssuePriority::all()->pluck('name', 'id')->toArray())
                        ->on('change', fn() => $this->emit('prioritySelected', $this->priority_id)),
                    Select::make('statuses')
                        ->label('Issue Statuses')
                        ->multiple()
                        ->options(IssueStatus::all()->pluck('name', 'id')->toArray())
                        ->on('change', fn() => $this->emit('statusSelected', $this->status_id)),
                    Toggle::make('includeNotAffectedIssues')
                        ->label('Show Only Affected Issues')
                        ->on('change', fn() => $this->emit('includeNotAffectedIssuesSelected', $this->includeNotAffectedIssues))
                        ->columnSpan(2),
                    Placeholder::make('search')->label(new HtmlString('&nbsp;'))->content(
                        new HtmlString('
                            <button type="button"
                                    wire:click="filter" wire:loading.attr="disabled"
                                    class="px-3 py-2 text-white rounded bg-primary-500 hover:bg-primary-600 disabled:bg-primary-300">
                                ' . __('Filter') . '
                            </button>
                            <button type="button"
                                    wire:click="resetFilters" wire:loading.attr="disabled"
                                    class="px-3 py-2 ml-2 text-white bg-gray-800 rounded hover:bg-gray-900 disabled:bg-gray-300">
                                ' . __('Reset filters') . '
                            </button>
                        ')
                    ),
                ]),
        ];
    }

    /**
     * Get the Issue Statuses
     * @return Collection Returns a collection of IssueStatus objects.
     */
    public function getStatuses(): Collection
    {
        // Build the statuses query
        $query = IssueStatus::q();

        // If the project is set, filter by project
        if ($this->project) {
            $query->where('project_id', $this->project->id);
        } else {
            $query->whereNull('project_id');

            // Return the statuses ordered by the Order value
            return $query->orderBy('order')->get()->map(function ($item) {
                // Query the issues
                $query = Issue::query();
                // If the project is set, filter by project
                if ($this->project) {
                    $query->where('project_id', $this->project->id);
                } else {
                    $query->whereNull('project_id');
                }

                // If the status is set, filter by status
                $query->where('status_id', $item->id);

                // Return the result
                return [
                    'id' => $item->id,
                    'title' => $item->name,
                    'color' => $item->color,
                    'size' => $query->count(),
                    'priority' => $item->priority,
                    'add_issue' => $this->is_default && Gate::allows('create', Issue::class)
                ];
            });
        }

        // Return the statuses ordered by the Order value
        return $query->orderBy('order')->get()->map(function ($item) {
            // Query the issues
            $query = Issue::query();
            // If the project is set, filter by project
            if ($this->project) {
                $query->where('project_id', $this->project->id);
            } else {
                $query->whereNull('project_id');
            }

            // If the status is set, filter by status
            $query->where('status_id', $item->id);

            // Return the result
            return [
                'id' => $item->id,
                'title' => $item->name,
                'color' => $item->color,
                'size' => $query->count(),
                'priority' => $item->priority,
                'add_issue' => $this->is_default && Gate::allows('create', Issue::class)
            ];
        });
    }

    /**
     * Get the records for the board.
     * @return Collection Returns a collection of Issue objects.
     */
    public function getRecords(): Collection
    {
        // Build the issues query
        $query = Issue::query();

        // If the project is set, filter by project
        if ($this->project) {
            $query->where('project_id', $this->project->id);
        } else {
            $query->whereNull('project_id');
        }

        // If the user is set, filter by user
        if ($this->users) {
            $query->whereIn('user_id', $this->users);
        }

        // If the type is set, filter by type
        if ($this->types) {
            $query->whereIn('type_id', $this->types);
        }

        // If the priority is set, filter by priority
        if ($this->priorities) {
            $query->whereIn('priority_id', $this->priorities);
        }

        // If the status is set, filter by status
        if ($this->statuses) {
            $query->whereIn('status_id', $this->statuses);
        }

        // If the includeNotAffectedIssues is set, filter by affected issues
        if (!$this->includeNotAffectedIssues) {
            $query->whereNotNull('status_id');
        }

        // Return the issues ordered by the Order value
        return $query->get()->map(function (Issue $item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'owner' => $item->owner,
                'slug' => $item->slug,
                'responsible' => $item->responsible,
                'project' => $item->project,
                'status' => $item->status->id,
                'priority' => $item->priority,
                'epic' => $item->epic,
                'relations' => $item->relations,
                'totalLoggedHours' => $item->totalLoggedSeconds ? $item->totalLoggedHours : null
            ];
        });
    }

    /**
     * Is the board a multi-project board?
     * @return bool
     */
    public function isMultiProject(): bool
    {
        return $this->project === null;
    }

    /**
     * Filter the records.
     * @return void
     */
    public function filter(): void
    {
        $this->getRecords();
    }

    /**
     * Reset the filters.
     * @return void
     */
    public function resetFilters(): void
    {
        $this->form->fill();
        $this->filter();
    }

    /**
     * Create a new issue.
     * @return void
     */
    public function createIssue(): void
    {
        $this->issue = true;
    }

    /**
     * Close the issue dialog.
     * @param bool $refresh
     * @return void
     */
    public function closeIssueDialog(bool $refresh): void
    {
        $this->issue = false;
        if ($refresh) {
            $this->filter();
        }
    }

    /**
     * Update Record
     * @param int $recordId
     * @param int $newIndex
     * @param int $newStatusId
     * @return void
     */
    public function updateRecord(int $recordId, int $newIndex, int $newStatusId): void
    {
        $issue = Issue::find($recordId);
        if ($issue) {
            $issue->order = $newIndex;
            $issue->status_id = $newStatusId;
            $issue->save();
            Filament::notify(__('Issue updated.'));
        }
    }

    /**
     * Get the board Heading for Kanban Boards.
     * @return string|Htmlable
     */
    protected function kanbanHeading(): string|Htmlable
    {
        $heading = '<div class="flex flex-col w-full gap-1">';
        $heading .= '<a href="' . route('filament.pages.board') . '"
                            class="text-xs font-medium text-primary-500 hover:underline">';
        $heading .= __('Back to board');
        $heading .= '</a>';
        $heading .= '<div class="flex flex-col gap-1">';
        $heading .= '<span>' . __('Kanban');
        if ($this->project) {
            $heading .= ' - ' . $this->project->name . '</span>';
        } else {
            $heading .= '</span><span class="text-xs text-gray-400">'
                . __('Only default statuses are listed when no projects selected')
                . '</span>';
        }
        $heading .= '</div>';
        $heading .= '</div>';
        return new HtmlString($heading);
    }

    /**
     * Get the board Heading for Scrum Boards.
     * @return string|Htmlable
     */
    protected function scrumHeading(): string|Htmlable
    {
        $heading = '<div class="flex flex-col w-full gap-1">';
        $heading .= '<a href="' . route('filament.pages.board') . '"
                            class="text-xs font-medium text-primary-500 hover:underline">';
        $heading .= __('Back to board');
        $heading .= '</a>';
        $heading .= '<div class="flex flex-col gap-1">';
        $heading .= '<span>' . __('Scrum');
        if ($this->project) {
            $heading .= ' - ' . $this->project->name . '</span>';
        } else {
            $heading .= '</span><span class="text-xs text-gray-400">'
                . __('Only default statuses are listed when no projects selected')
                . '</span>';
        }
        $heading .= '</div>';
        $heading .= '</div>';
        return new HtmlString($heading);
    }

    /**
     * Get the Scrum Subheading.
     * @return string|Htmlable|null
     */
    protected function scrumSubheading(): string|Htmlable|null
    {
        if ($this->project?->currentSprint) {
            return new HtmlString(
                '<div class="flex flex-col w-full gap-1">'
                    . '<div class="flex items-center w-full gap-2">'
                    . '<span class="px-2 py-1 text-sm text-white rounded bg-danger-500">'
                    . $this->project->currentSprint->name
                    . '</span>'
                    . '<span class="text-xs text-gray-400">'
                    . __('Started at:') . ' ' . $this->project->currentSprint->started_at->format(__('Y-m-d')) . ' - '
                    . __('Ends at:') . ' ' . $this->project->currentSprint->ends_at->format(__('Y-m-d')) . ' - '
                    . ($this->project->currentSprint->remaining ?
                        (
                            __('Remaining:') . ' ' . $this->project->currentSprint->remaining . ' ' . __('days'))
                        : ''
                    )
                    . '</span>'
                    . '</div>'
                    . ($this->project->nextSprint ? '<span class="text-xs font-medium text-primary-500">'
                        . __('Next sprint:') . ' ' . $this->project->nextSprint->name . ' - '
                        . __('Starts at:') . ' ' . $this->project->nextSprint->starts_at->format(__('Y-m-d'))
                        . ' (' . __('in') . ' ' . $this->project->nextSprint->starts_at->diffForHumans() . ')'
                        . '</span>'
                        . '</span>' : '')
                    . '</div>'
            );
        } else {
            return null;
        }
    }
}

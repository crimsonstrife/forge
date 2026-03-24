<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use App\Models\User;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class AssigneeWorkloadTable extends BaseWidget
{
    protected static ?string $heading = 'Assignee Workload';
    public ?string $projectId = null;
    public ?string $dateTo = null;

    protected function getTableQuery(): Builder|null
    {
        if ($this->projectId === null) {
            return User::query()->whereRaw('0 = 1');
        }

        $asOf = $this->dateTo
            ? Carbon::parse($this->dateTo)->endOfDay()
            : now();

        $unassignedKey = 'unassigned:'.$this->projectId;

        $aggregate = Issue::query()
            ->leftJoin('users', 'users.id', '=', 'issues.assignee_id')
            ->join('issue_statuses', 'issue_statuses.id', '=', 'issues.issue_status_id')
            ->where('issues.project_id', $this->projectId)
            ->selectRaw(
                "COALESCE(issues.assignee_id, ?) as id,
                issues.assignee_id,
                COALESCE(users.name, 'Unassigned') as name,
                COUNT(*) AS total,
                SUM(CASE WHEN issue_statuses.is_done = 0 THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN issue_statuses.is_done = 0 THEN COALESCE(issues.story_points, 0) ELSE 0 END) AS active_points,
                SUM(CASE WHEN issue_statuses.is_done = 0 AND issues.due_at IS NOT NULL AND issues.due_at < ? THEN 1 ELSE 0 END) AS overdue",
                [$unassignedKey, $asOf]
            )
            ->groupBy('issues.assignee_id', 'users.name');

        return User::query()
            ->fromSub($aggregate, 'users')
            ->select('users.*')
            ->orderByDesc('active')
            ->orderBy('name');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Assignee')
                ->sortable(false),

            Tables\Columns\TextColumn::make('active')
                ->label('Active')
                ->sortable(false),

            Tables\Columns\TextColumn::make('active_points')
                ->label('Points')
                ->sortable(false),

            Tables\Columns\TextColumn::make('overdue')
                ->label('Overdue')
                ->sortable(false),

            Tables\Columns\TextColumn::make('total')
                ->label('Total')
                ->sortable(false),
        ];
    }
}

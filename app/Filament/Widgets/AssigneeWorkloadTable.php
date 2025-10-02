<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use App\Models\User;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class AssigneeWorkloadTable extends BaseWidget
{
    protected static ?string $heading = 'Assignee Workload';
    public ?string $projectId = null;

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder| Relation | null
    {
        if ($this->projectId === null) {
            return User::query()->whereRaw('0 = 1');
        }

        $agg = Issue::query()
            ->join('issue_statuses', 'issue_statuses.id', '=', 'issues.issue_status_id')
            ->where('issues.project_id', $this->projectId)
            ->selectRaw('issues.assignee_id, COUNT(*) AS total, SUM(CASE WHEN issue_statuses.is_done = 0 THEN 1 ELSE 0 END) AS active')
            ->groupBy('issues.assignee_id');

        return User::query()
            ->leftJoinSub($agg, 'agg', 'agg.assignee_id', '=', 'users.id')
            ->selectRaw('users.id, users.name, COALESCE(agg.active, 0) AS active, COALESCE(agg.total, 0) AS total')
            ->orderByDesc('active')
            ->orderBy('users.id');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Assignee')
                ->default('Unassigned')
                ->sortable(false),

            Tables\Columns\TextColumn::make('active')
                ->label('Active')
                ->sortable(false),

            Tables\Columns\TextColumn::make('total')
                ->label('Total')
                ->sortable(false),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class WipAgingTable extends BaseWidget
{
    protected static ?string $heading = 'WIP Aging';
    public ?string $projectId = null;

    protected function getTableQuery(): Builder|Relation|null
    {
        if ($this->projectId === null) {
            return Issue::query()->whereRaw('0 = 1');
        }

        return Issue::query()
            ->select([
                'issues.id', 'issues.key', 'issues.title', 'issues.assignee_id',
                'issues.issue_status_id', 'issue_statuses.name as status_name',
                'issue_metrics.age_min',
            ])
            ->join('issue_statuses', 'issue_statuses.id', '=', 'issues.issue_status_id')
            ->join('issue_metrics', 'issue_metrics.issue_id', '=', 'issues.id')
            ->where('issues.project_id', $this->projectId)
            ->where('issue_statuses.is_done', false)
            ->orderByDesc('issue_metrics.age_min');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('key')->label('Key')->searchable(),
            Tables\Columns\TextColumn::make('title')->label('Title')->limit(60)->searchable(),
            Tables\Columns\TextColumn::make('status_name')->label('Status'),
            Tables\Columns\TextColumn::make('assignee_id')
                ->label('Assignee')
                ->formatStateUsing(static function ($id): string {
                    $u = \App\Models\User::query()->select(['id','name'])->find($id);
                    return $u?->name ?? 'Unassigned';
                }),
            Tables\Columns\TextColumn::make('age_min')
                ->label('Age')
                ->formatStateUsing(static function (int $mins): string {
                    $d = intdiv($mins, 1440);
                    $h = intdiv($mins % 1440, 60);
                    return $d > 0 ? "{$d}d {$h}h" : "{$h}h";
                }),
        ];
    }
}

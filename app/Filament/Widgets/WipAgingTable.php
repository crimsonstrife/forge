<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class WipAgingTable extends BaseWidget
{
    protected static ?string $heading = 'WIP Aging';
    public ?string $projectId = null;
    public ?string $dateTo = null;

    protected function getTableQuery(): Builder|Relation|null
    {
        if ($this->projectId === null) {
            return Issue::query()->whereRaw('0 = 1');
        }

        return Issue::query()
            ->select([
                'issues.id',
                'issues.key',
                'issues.summary as title',
                'issues.assignee_id',
                'users.name as assignee_name',
                'issues.due_at',
                'issues.issue_status_id', 'issue_statuses.name as status_name',
                'issue_metrics.first_started_at',
                'issues.created_at',
            ])
            ->leftJoin('users', 'users.id', '=', 'issues.assignee_id')
            ->join('issue_statuses', 'issue_statuses.id', '=', 'issues.issue_status_id')
            ->leftJoin('issue_metrics', 'issue_metrics.issue_id', '=', 'issues.id')
            ->where('issues.project_id', $this->projectId)
            ->where('issue_statuses.is_done', false)
            ->orderBy('issues.due_at')
            ->orderBy('issue_metrics.first_started_at')
            ->orderBy('issues.created_at');
    }

    protected function getTableColumns(): array
    {
        $asOf = $this->dateTo
            ? Carbon::parse($this->dateTo)->endOfDay()
            : now();

        return [
            Tables\Columns\TextColumn::make('key')->label('Key')->searchable(),
            Tables\Columns\TextColumn::make('title')->label('Title')->limit(60)->searchable(),
            Tables\Columns\TextColumn::make('status_name')->label('Status'),
            Tables\Columns\TextColumn::make('assignee_name')
                ->label('Assignee')
                ->default('Unassigned'),
            Tables\Columns\TextColumn::make('first_started_at')
                ->label('Age')
                ->formatStateUsing(static function ($state, Issue $record) use ($asOf): string {
                    $startedAt = $state
                        ? Carbon::parse($state)
                        : Carbon::parse($record->created_at);
                    $mins = $startedAt->diffInMinutes($asOf);
                    $days = intdiv($mins, 1440);
                    $hours = intdiv($mins % 1440, 60);

                    return $days > 0 ? "{$days}d {$hours}h" : "{$hours}h";
                }),
            Tables\Columns\TextColumn::make('due_at')
                ->label('Due')
                ->dateTime('M j, Y')
                ->placeholder('No due date'),
            Tables\Columns\TextColumn::make('due_risk')
                ->label('Risk')
                ->state(static fn (Issue $record) => $record->due_at)
                ->formatStateUsing(static function ($state) use ($asOf): string {
                    if (! $state) {
                        return 'No due date';
                    }

                    $dueAt = Carbon::parse($state);

                    if ($dueAt->lt($asOf)) {
                        return $dueAt->diffInDays($asOf).'d overdue';
                    }

                    return 'On track';
                }),
        ];
    }
}

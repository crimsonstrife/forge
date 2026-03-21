<?php

namespace App\Filament\Widgets;

use App\Models\Issue;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Carbon;

class OverdueWorkTable extends BaseWidget
{
    protected static ?string $heading = 'Overdue Work';

    public ?string $projectId = null;
    public ?string $dateTo = null;

    protected function getTableQuery(): Builder|Relation|null
    {
        if ($this->projectId === null) {
            return Issue::query()->whereRaw('0 = 1');
        }

        $asOf = $this->dateTo
            ? Carbon::parse($this->dateTo)->endOfDay()
            : now();

        return Issue::query()
            ->select([
                'issues.id',
                'issues.key',
                'issues.summary as title',
                'issues.due_at',
                'users.name as assignee_name',
                'issue_statuses.name as status_name',
            ])
            ->leftJoin('users', 'users.id', '=', 'issues.assignee_id')
            ->join('issue_statuses', 'issue_statuses.id', '=', 'issues.issue_status_id')
            ->where('issues.project_id', $this->projectId)
            ->where('issue_statuses.is_done', false)
            ->whereNotNull('issues.due_at')
            ->where('issues.due_at', '<', $asOf)
            ->orderBy('issues.due_at');
    }

    protected function getTableColumns(): array
    {
        $asOf = $this->dateTo
            ? Carbon::parse($this->dateTo)->endOfDay()
            : now();

        return [
            Tables\Columns\TextColumn::make('key')
                ->label('Key')
                ->searchable(),
            Tables\Columns\TextColumn::make('title')
                ->label('Title')
                ->limit(60)
                ->searchable(),
            Tables\Columns\TextColumn::make('status_name')
                ->label('Status'),
            Tables\Columns\TextColumn::make('assignee_name')
                ->label('Assignee')
                ->default('Unassigned'),
            Tables\Columns\TextColumn::make('due_at')
                ->label('Due')
                ->dateTime('M j, Y'),
            Tables\Columns\TextColumn::make('days_overdue')
                ->label('Overdue')
                ->state(static fn (Issue $record) => $record->due_at)
                ->formatStateUsing(static fn ($state): string => Carbon::parse($state)->diffInDays($asOf).'d'),
        ];
    }
}

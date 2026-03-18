<?php

namespace App\Services\Projects;

use App\Models\IssuePriority;
use App\Models\IssueStatus;
use App\Models\IssueType;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

final class ProjectSchemeSeeder
{
    public function seed(Project $project): void
    {
        if (! $project->issueTypes()->exists()) {
            $this->insertPivotRows(
                table: 'project_issue_types',
                rows: IssueType::query()
                    ->orderBy('name')
                    ->pluck('id')
                    ->map(fn ($id, $index) => [
                        'project_id' => $project->getKey(),
                        'issue_type_id' => $id,
                        'order' => $index,
                        'is_default' => $index === 0,
                    ])
                    ->all()
            );
        }

        if (! $project->issueStatuses()->exists()) {
            $statuses = IssueStatus::query()->orderBy('order')->get();

            $this->insertPivotRows(
                table: 'project_issue_statuses',
                rows: $statuses->map(fn (IssueStatus $status, $index) => [
                    'project_id' => $project->getKey(),
                    'issue_status_id' => $status->getKey(),
                    'order' => $index,
                    'is_initial' => $index === 0 && ! $status->is_done,
                    'is_default_done' => $status->is_done,
                ])->all()
            );
        }

        if (! $project->issuePriorities()->exists()) {
            $this->insertPivotRows(
                table: 'project_issue_priorities',
                rows: IssuePriority::query()
                    ->orderBy('order')
                    ->pluck('id')
                    ->map(fn ($id, $index) => [
                        'project_id' => $project->getKey(),
                        'issue_priority_id' => $id,
                        'order' => $index,
                        'is_default' => $index === 2,
                    ])
                    ->all()
            );
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    private function insertPivotRows(string $table, array $rows): void
    {
        if ($rows === []) {
            return;
        }

        $hasId = Schema::hasColumn($table, 'id');
        $now = now();

        $payload = array_map(function (array $row) use ($hasId, $now): array {
            if ($hasId) {
                $row['id'] = (string) Str::uuid();
            }

            $row['created_at'] = $now;
            $row['updated_at'] = $now;

            return $row;
        }, $rows);

        DB::table($table)->insert($payload);
    }
}

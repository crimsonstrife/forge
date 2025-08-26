<?php

namespace App\Services\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Throwable;

final class ProjectSchemeCloner
{
    /**
     * @throws Throwable
     */
    public function cloneSchemes(string $fromProjectId, string $toProjectId): void
    {
        /** @var Project $from */
        /** @var Project $to */
        $from = Project::findOrFail($fromProjectId);
        $to   = Project::findOrFail($toProjectId);

        DB::transaction(static function () use ($from, $to) {
            // types
            $map = $from->issueTypes()->get()->mapWithKeys(
                fn ($row) => [$row->id => ['order' => $row->pivot->order, 'is_default' => $row->pivot->is_default]]
            )->all();
            $to->issueTypes()->sync($map);

            // statuses
            $map = $from->issueStatuses()->get()->mapWithKeys(
                fn ($row) => [$row->id => [
                    'order' => $row->pivot->order,
                    'is_initial' => $row->pivot->is_initial,
                    'is_default_done' => $row->pivot->is_default_done,
                ]]
            )->all();
            $to->issueStatuses()->sync($map);

            // priorities
            $map = $from->issuePriorities()->get()->mapWithKeys(
                fn ($row) => [$row->id => ['order' => $row->pivot->order, 'is_default' => $row->pivot->is_default]]
            )->all();
            $to->issuePriorities()->sync($map);

            // transitions
            $to->statusTransitions()->delete();
            $rows = $from->statusTransitions()->get(['from_status_id','to_status_id','is_global','issue_type_id'])
                ->map(fn ($r) => $r->toArray())->all();
            if ($rows) {
                $to->statusTransitions()->createMany($rows);
            }
        });
    }
}

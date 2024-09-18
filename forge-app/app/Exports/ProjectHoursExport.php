<?php

namespace App\Exports;

use App\Models\Projects\Project;
use App\Models\Issues\Issue;
use App\Models\Issues\IssueHour;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class ProjectHoursExport
 *
 * This class represents an export of project hours data.
 * It implements the FromCollection and WithHeadings interfaces.
 *
 * @package App\Exports
 */
class ProjectHoursExport implements FromCollection, WithHeadings
{
    public Project $project;

    /**
     * Constructor
     * @param \App\Models\Projects\Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    /**
     * Get the headings
     * @return array
     */
    public function headings(): array
    {
        return [
            '#',
            'Issue',
            'User',
            'Time',
            'Hours',
            'Activity',
            'Date',
        ];
    }

    /**
     * @return Collection
     */
    public function collection(): Collection
    {
        $collection = collect();
        $this->project->issues
            ->filter(fn($issue) => $issue->hours()->count())
            ->each(
                fn($issue) =>
                $issue->hours
                    ->each(fn(IssueHour $item) => $collection->push([
                        '#' => $item->issue->code,
                        'issue' => $item->issue->name,
                        'user' => $item->user->name,
                        'time' => $item->forHumans,
                        'hours' => number_format($item->value, 2, ',', ' '),
                        'activity' => $item->activity ? $item->activity->name : '-',
                        'date' => $item->created_at->format(__('Y-m-d g:i A')),
                    ]))
            );
        return $collection;
    }
}

<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Issue;
use App\Models\IssueHour;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TimesheetExport implements FromCollection, WithHeadings
{
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function headings(): array
    {
        return [
            '#',
            'Project',
            'Issue',
            'Details',
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

        $hours = IssueHour::where('user_id', auth()->user()->id)
            ->whereBetween('created_at', [$this->params['start_date'], $this->params['end_date']])
            ->get();

        foreach ($hours as $item) {
            $collection->push([
                '#' => $item->issue->code,
                'project' => $item->issue->project->name,
                'issue' => $item->issue->name,
                'details' => $item->comment,
                'user' => $item->user->name,
                'time' => $item->forHumans,
                'hours' => number_format($item->value, 2, ',', ' '),
                'activity' => $item->activity ? $item->activity->name : '-',
                'date' => $item->created_at->format(__('Y-m-d g:i A')),
            ]);
        }

        return $collection;
    }
}

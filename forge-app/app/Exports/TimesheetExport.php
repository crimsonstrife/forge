<?php

namespace App\Exports;

use App\Models\Project;
use App\Models\Issue;
use App\Models\IssueHour;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class TimesheetExport
 *
 * This class represents a timesheet export. It implements the FromCollection and WithHeadings interfaces.
 * The TimesheetExport class is responsible for generating a collection of data and defining the headings for the exported timesheet.
 *
 * @package App\Exports
 */
class TimesheetExport implements FromCollection, WithHeadings
{
    protected array $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    /**
     * Returns an array of headings for the TimesheetExport class.
     *
     * @return array The array of headings.
     */
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
        $hours = IssueHour::where('user_id', Auth::user()->id)
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

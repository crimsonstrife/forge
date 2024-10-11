<?php

namespace App\Exports;

use App\Models\Issues\Issue;
use App\Models\Issues\IssueHour;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class IssueHoursExport
 *
 * This class represents an export of issue hours data.
 * It implements the FromCollection and WithHeadings interfaces.
 */
class IssueHoursExport implements FromCollection, WithHeadings
{
    public Issue $issue;

    /**
     * Constructor
     * @param \App\Models\Issues\Issue $issue
     */
    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
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
            'Comment',
        ];
    }

    /**
     * Get the collection of data
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->issue->hours
            ->map(fn (IssueHour $item) => [
                '#' => $item->ticket->code,
                'ticket' => $item->ticket->name,
                'user' => $item->user->name,
                'time' => $item->forHumans,
                'hours' => number_format($item->value, 2, ',', ' '),
                'activity' => $item->activity ? $item->activity->name : '-',
                'date' => $item->created_at->format(__('Y-m-d g:i A')),
                'comment' => $item->comment
            ]);
    }
}

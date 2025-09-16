<?php

namespace App\Http\Controllers;

use App\Models\Issue;
use App\Models\Project;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

final class ProjectCalendarController extends Controller
{
    public function __invoke(Request $request, Project $project): Response
    {
        $this->authorize('view', $project);

        $issues = Issue::query()
            ->where('project_id', $project->id)
            ->where(function ($q) {
                $q->whereNotNull('starts_at')->orWhereNotNull('due_at');
            })
            ->orderBy('starts_at')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Forge//Project Calendar//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($issues as $i) {
            $uid = "issue-{$i->id}@forge";
            $dtstamp = CarbonImmutable::now('UTC')->format('Ymd\THis\Z');

            $start = $i->starts_at?->utc()->format('Ymd\THis\Z');
            $end   = $i->due_at?->utc()->format('Ymd\THis\Z');

            // If only due_at, make it an all-day on due date
            $vevent = [
                'BEGIN:VEVENT',
                "UID:{$uid}",
                "DTSTAMP:{$dtstamp}",
                "SUMMARY:{$i->key} — " . addcslashes($i->summary, ",;"),
                "URL:" . route('projects.issues.show', ['project' => $project, 'issue' => $i]),
            ];

            if ($start && $end) {
                $vevent[] = "DTSTART:{$start}";
                $vevent[] = "DTEND:{$end}";
            } elseif ($end) {
                $vevent[] = "DTSTART;VALUE=DATE:" . $i->due_at->utc()->format('Ymd');
                $vevent[] = "DTEND;VALUE=DATE:" . $i->due_at->utc()->addDay()->format('Ymd');
            } elseif ($start) {
                $vevent[] = "DTSTART:{$start}";
                $vevent[] = "DTEND:" . $i->starts_at->utc()->addHour()->format('Ymd\THis\Z');
            }

            $vevent[] = 'END:VEVENT';

            // Append the VEVENT lines directly to $lines
            foreach ($vevent as $line) {
                $lines[] = $line;
            }
        }

        $lines[] = 'END:VCALENDAR';

        return response(implode("\r\n", $lines), 200, [
            'Content-Type' => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="project-'.$project->id.'-calendar.ics"',
        ]);
    }
}

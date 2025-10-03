<?php

use App\Models\TimeEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

use function Laravel\Folio\{name, render};

name('stream.now.public');

render(function (Request $request) {
    abort_unless(URL::hasValidSignature($request), 403);

    $userId = (string) $request->string('user');

    $running = TimeEntry::query()
        ->where('user_id', $userId)
        ->whereNull('ended_at')
        ->with(['issue:id,summary,project_id,key', 'issue.project:id,key'])
        ->latest('started_at')
        ->first();

    $payload = $running ? [
        'user_id'         => $userId,
        'issue_id'        => (string) $running->issue_id,
        'issue_key'       => $running->issue?->key,
        'issue_summary'   => $running->issue?->summary,
        'project_id'      => $running->issue?->project_id,
        'project_key'     => $running->issue?->project?->key,
        'started_at'      => optional($running->started_at)?->toIso8601String(),
        'elapsed_seconds' => max(0, now()->getTimestamp() - $running->started_at->getTimestamp()),
        'issue_url'       => $running->issue
            ? route('issues.show', ['project' => $running->issue->project_id, 'issue' => $running->issue])
            : null,
        'updated_at'      => now()->toIso8601String(),
    ] : null;

    return response()
        ->json(['running' => $payload])
        ->header('Cache-Control', 'no-store, max-age=0');
});

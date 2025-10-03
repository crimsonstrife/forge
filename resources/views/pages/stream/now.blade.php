<?php

use App\Models\TimeEntry;
use App\Settings\PersonalizationSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function Laravel\Folio\{name, middleware, render};

name('stream.now');
middleware(['auth', 'verified']);

render(function (Request $request) {
    /** @var PersonalizationSettings $prefs */
    $prefs = app(PersonalizationSettings::class);

    if (!(bool)($prefs->streamer_mode ?? false)) {
        abort(404);
    }

    $userId = (string)Auth::id();

    $running = TimeEntry::query()
        ->where('user_id', $userId)
        ->whereNull('ended_at')
        ->with(['issue:id,summary,project_id,key', 'issue.project:id,key'])
        ->latest('started_at')
        ->first();

    $payload = $running ? [
        'user_id' => $userId,
        'issue_id' => (string)$running->issue_id,
        'issue_key' => $running->issue?->key,
        'issue_summary' => $running->issue?->summary,
        'project_id' => $running->issue?->project_id,
        'project_key' => $running->issue?->project?->key,
        'started_at' => optional($running->started_at)?->toIso8601String(),
        'elapsed_seconds' => max(0, now()->getTimestamp() - $running->started_at->getTimestamp()),
        'issue_url' => $running->issue
            ? route('issues.show', ['project' => $running->issue->project_id, 'issue' => $running->issue])
            : null,
        'updated_at' => now()->toIso8601String(),
    ] : null;

    return response()
        ->json(['running' => $payload])
        ->header('Cache-Control', 'no-store, max-age=0');
});

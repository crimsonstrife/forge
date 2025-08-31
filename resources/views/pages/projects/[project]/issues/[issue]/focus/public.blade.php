<?php

use App\Models\Issue;
use App\Models\Project;
use App\Models\TimeEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use function Laravel\Folio\{name, middleware, render};

name('issues.focus.public');
// No auth – signed link required
middleware(['signed']);

render(function (View $view, Request $request, Project $project, Issue $issue): void {
    $running = TimeEntry::query()
        ->where('issue_id', $issue->id)
        ->whereNull('ended_at')
        ->latest('started_at')
        ->first();

    $startedAt = $running?->started_at?->getTimestamp();
    $isRunning = $running !== null;

    $payload = [
        'is_running' => $isRunning,
        'started_at' => $startedAt,
        'server_now' => now()->getTimestamp(),
    ];

    if ($request->wantsJson() || $request->boolean('json')) {
        echo response()->json($payload)->send();
        exit;
    }

    $view->with(array_merge([
        'transparent' => false,
        'compact' => false,
        'pollMs' => 5000,
    ], [
        'isRunning' => $isRunning,
        'startedAt' => $startedAt,
    ]));
});
?>
@php
    // Safe defaults in case the page is parsed before render() binds data
    $transparent = $transparent ?? false;
    $compact = $compact ?? false;
    $pollMs = $pollMs ?? 5000;
    $isRunning = $isRunning ?? false;
    $startedAt = $startedAt ?? null;
@endphp
    <!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Focus Timer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <style>
        html, body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol;
            background: {{ $transparent ? 'transparent' : '#0b1020' }};
            color: #fff;
        }

        .wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .timer {
            font-weight: 700;
            letter-spacing: .02em;
            {{ $compact ? 'font-size: 32px; padding: 6px 10px; border-radius: 10px;' : 'font-size: 48px; padding: 10px 16px; border-radius: 14px;' }}
   background: {{ $transparent ? 'transparent' : 'rgba(255,255,255,.08)' }};
            border: {{ $transparent ? 'none' : '1px solid rgba(255,255,255,.15)' }};
            backdrop-filter: blur(8px);
        }

        .paused {
            opacity: .7
        }

        .badge {
            font-size: 12px;
            margin-left: 10px;
            opacity: .8
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="timer" id="t">
        <span id="time">00:00:00</span>
        <span id="state" class="badge"></span>
    </div>
</div>

<script>
    (function () {
        const startedAt = @json(($isRunning && $startedAt) ? (int) $startedAt : null, JSON_THROW_ON_ERROR);
        const pollMs = @json((int) $pollMs, JSON_THROW_ON_ERROR);
        let isRunning = @json((bool) $isRunning, JSON_THROW_ON_ERROR);
        let seconds = 0;

        const elTime = document.getElementById('time');
        const elState = document.getElementById('state');
        const elTimer = document.getElementById('t');

        function fmt(total) {
            total = Math.max(0, Math.floor(Number(total) || 0));
            const h = String(Math.floor(total / 3600)).padStart(2, '0');
            const m = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
            const s = String(Math.floor(total % 60)).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        function setState(run) {
            isRunning = !!run;
            elState.textContent = isRunning ? 'LIVE' : 'PAUSED';
            elTimer.classList.toggle('paused', !isRunning);
        }

        // Seed
        if (startedAt) {
            seconds = Math.max(0, Math.floor(Date.now() / 1000) - startedAt);
            setState(true);
            elTime.textContent = fmt(seconds);
        } else {
            setState(false);
            elTime.textContent = fmt(0);
        }

        // Local tick
        setInterval(() => {
            if (isRunning) {
                seconds += 1;
                elTime.textContent = fmt(seconds);
            }
        }, 1000);

        // Poll server for authoritative state
        async function poll() {
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('json', '1');
                const res = await fetch(url.toString(), {headers: {'Accept': 'application/json'}, cache: 'no-store'});
                const data = await res.json();

                const running = !!data.is_running;
                if (running) {
                    const now = Math.floor(Date.now() / 1000);
                    const serverStart = Number(data.started_at || 0);
                    const serverNow = Number(data.server_now || now);
                    const serverElapsed = Math.max(0, serverNow - serverStart);

                    // If server is ahead, jump forward; if behind, ignore to avoid flicker
                    if (serverElapsed > seconds) {
                        seconds = serverElapsed;
                        elTime.textContent = fmt(seconds);
                    }
                } else {
                    seconds = 0;
                    elTime.textContent = fmt(0);
                }

                if (running !== isRunning) {
                    setState(running);
                }
            } catch (_) {
                /* ignore network hiccups */
            } finally {
                setTimeout(poll, pollMs);
            }
        }

        setTimeout(poll, pollMs);
    })();
</script>
</body>
</html>

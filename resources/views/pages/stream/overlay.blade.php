<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function Laravel\Folio\{name, render};

name('stream.overlay');

render(function (Request $request) {
    // If ?url is missing, default to the authenticated JSON when logged in.
    $jsonUrl = (string) $request->string('url', '');
    if ($jsonUrl === '' && Auth::check()) {
        $jsonUrl = route('stream.now');
    }

    view()->share('jsonUrl', $jsonUrl);
});
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Now Working On</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html,body{background:transparent;margin:0}
        body{font:16px/1.4 system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif}
        .wrap{padding:.5rem .75rem; background: rgba(0,0,0,.66); color:#fff; border-radius:.5rem;
            display:inline-flex; gap:.5rem; align-items:center; max-width:95vw}
        .key{opacity:.85; font-family: ui-monospace, Menlo, Consolas, monospace; white-space:nowrap}
        .summary{white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:70vw}
        .time{opacity:.85}
        .warn{background:#fff3cd;color:#664d03;border:1px solid #ffecb5;border-radius:.5rem;padding:.5rem .75rem;margin:.5rem}
    </style>
</head>
<body>
<div class="wrap" id="box">
    <span class="key" id="key">—</span>
    <span class="summary" id="summary">Loading…</span>
    <span class="time" id="elapsed"></span>
</div>

<div id="warning" class="warn" style="display:none">
    Missing <code>?url=</code> param. While logged in, this page will default to your auth feed.
    For public/OBS, open the overlay with:
    <code>{{ route('stream.overlay') }}?url={{ urlencode(URL::signedRoute('stream.now.public', ['user' => auth()->id()])) }}</code>
</div>

<script>
    const url = @json($jsonUrl);
    let startedAt = null;

    const fmt = s => {
        s = Math.max(0, Math.floor(Number(s)||0));
        const h = String(Math.floor(s/3600)).padStart(2,'0');
        const m = String(Math.floor((s%3600)/60)).padStart(2,'0');
        const a = String(s%60).padStart(2,'0');
        return `${h}:${m}:${a}`;
    };

    if (!url) {
        // Friendly inline guidance, no exception/500s.
        document.getElementById('key').textContent = '';
        document.getElementById('summary').textContent = 'Add ?url=… to use this overlay.';
        document.getElementById('elapsed').textContent = '';
        const w = document.getElementById('warning');
        if (w) w.style.display = 'block';
    } else {
        async function poll() {
            try {
                const r = await fetch(url, {cache:'no-store'});
                const j = await r.json();
                const run = j?.running;
                if (!run) {
                    document.getElementById('key').textContent = '—';
                    document.getElementById('summary').textContent = 'No active timer';
                    document.getElementById('elapsed').textContent = '';
                    startedAt = null;
                    return;
                }
                document.getElementById('key').textContent = run.issue_key || '';
                document.getElementById('summary').textContent = run.issue_summary || '';
                const serverElapsed = Number(run.elapsed_seconds || 0);
                startedAt = Date.now()/1000 - serverElapsed;
                document.getElementById('elapsed').textContent = fmt(serverElapsed);
            } catch (e) {
                // Keep overlay resilient during hiccups
            }
        }

        // Smooth local tick
        setInterval(() => {
            if (startedAt != null) {
                const now = Date.now()/1000;
                document.getElementById('elapsed').textContent = fmt(now - startedAt);
            }
        }, 1000);

        poll(); setInterval(poll, 5000);
    }
</script>
</body>
</html>

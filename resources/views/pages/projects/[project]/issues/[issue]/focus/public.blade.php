<?php

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use function Laravel\Folio\{name, render};

name('issues.focus.public');

render(function (View $view, Request $request, Issue $issue) {
    abort_unless(URL::hasValidSignature($request), 403);
    $view->with('issue', $issue->loadMissing('project'));
});
?>
    <!doctype html><html lang="en"><head>
    <meta charset="utf-8"><title>Focus</title>
    @vite(['resources/js/app.js','resources/css/app.css']) @livewireStyles
</head><body class="bg-body p-3">
<div class="container"><livewire:issues.focus-timer :issue="$issue"/></div>
@livewireScripts
</body></html>

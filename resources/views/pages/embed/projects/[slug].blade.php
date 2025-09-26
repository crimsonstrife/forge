<?php
use Illuminate\View\View;
use App\Models\Project;

use function Laravel\Folio\{name, render};

name('embed.projects.show');

render(function (View $view, string $slug) {
    $project = Project::where('public_slug', $slug)->firstOrFail();
    return $view->with('project', $project);
});
?>
<!doctype html>
<html lang="en" data-theme="{{ $theme }}">
<head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $project->name }} · Public Tracker</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <style>html,body{background:transparent}</style>
</head>
<body class="bg-transparent">
<livewire:public.project-kanban :project="$project" />
</body>
</html>

<?php

use App\Models\Issue;
use Illuminate\View\View;

use function Laravel\Folio\{name, middleware, render};

name('issues.focus');
middleware(['auth','verified']);

render(function (View $view, Issue $issue) {
    $view->with('issue', $issue->loadMissing('project'));
});
?>
<x-app-layout>
    <x-slot name="header"><h1 class="h5 mb-0">Focus: {{ $issue->summary }}</h1></x-slot>
    <div class="container py-3"><livewire:issues.focus-timer :issue="$issue"/></div>
</x-app-layout>

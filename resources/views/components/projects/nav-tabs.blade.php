@php use App\Models\Project; @endphp
@props(['project'])

@php
    /** @var Project $project */
    $tabs = [
        ['label' => 'Overview',   'route' => 'projects.show'],
        ['label' => 'Board',      'route' => 'projects.board'],
        ['label' => 'Scrum',      'route' => 'projects.scrum'],
        ['label' => 'Calendar',   'route' => 'projects.calendar'],
        ['label' => 'Timeline',   'route' => 'projects.timeline'],
        ['label' => 'Code',       'route' => 'projects.code'],
        ['label' => 'Transitions','route' => 'projects.transitions'],
    ];
@endphp

<ul class="nav nav-tabs card-header-tabs">
    @foreach ($tabs as $t)
        <li class="nav-item">
            <a
                class="nav-link {{ request()->routeIs($t['route']) ? 'active' : '' }}"
                href="{{ route($t['route'], ['project' => $project]) }}"
            >
                {{ __($t['label']) }}
            </a>
        </li>
    @endforeach
</ul>

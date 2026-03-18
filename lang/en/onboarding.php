<?php

return [
    'tours' => [
        'main_app' => [
            'label' => 'Main app',
            'steps' => [
                'dashboard' => [
                    'title' => 'Your dashboard',
                    'body' => 'Start here for assigned work, due dates, and recent activity across Forge.',
                ],
                'issues' => [
                    'title' => 'Issues keep work moving',
                    'body' => 'Issues are the core units of work in Forge, and this panel keeps your assigned items close at hand.',
                ],
                'projects' => [
                    'title' => 'Projects',
                    'body' => 'Projects organize delivery, planning, and issue workflows for each initiative.',
                ],
                'goals' => [
                    'title' => 'Goals',
                    'body' => 'Goals keep longer-term outcomes visible across projects and issues.',
                ],
                'support' => [
                    'title' => 'Service desk',
                    'body' => 'Support tickets help staff triage inbound requests and connect them back to internal work.',
                ],
                'search' => [
                    'title' => 'Search',
                    'body' => 'Search is the fastest way to jump to projects, issues, organizations, and goals.',
                ],
                'create' => [
                    'title' => 'Create from anywhere',
                    'body' => 'Use Create to add new issues, projects, organizations, and goals without leaving the current page.',
                ],
                'teams' => [
                    'title' => 'Teams',
                    'body' => 'Switch teams, manage membership, and open team settings from here.',
                ],
                'account' => [
                    'title' => 'Account and help',
                    'body' => 'Manage your profile, API tokens, and relaunch this tour or the Getting Started guide later.',
                ],
            ],
        ],
    ],
    'ui' => [
        'step_counter' => 'Step :current of :total',
        'next' => 'Next',
        'back' => 'Back',
        'finish' => 'Finish',
        'open_page' => 'Open page',
        'end_tour' => 'End tour',
        'dismiss_aria_label' => 'End tour',
        'continue_to_page' => 'Continue to the next page to keep the tour moving.',
        'collapsed_navigation_hint' => 'This step may be tucked inside a collapsible navigation area on smaller screens.',
    ],
];

<?php

return [
    'tours' => [
        'main-app' => [
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
        'project-detail' => [
            'label' => 'Project walkthrough',
            'steps' => [
                'header' => [
                    'title' => 'Project overview',
                    'body' => 'This header gives you the project identity and the most common project-level actions.',
                ],
                'tabs' => [
                    'title' => 'Project views',
                    'body' => 'Move between overview, board, scrum, calendar, timeline, code, and transition screens from the project tabs.',
                ],
                'status_summary' => [
                    'title' => 'Status summary',
                    'body' => 'Use the status summary to see how work is distributed across the workflow at a glance.',
                ],
                'assigned_issues' => [
                    'title' => 'Assigned work',
                    'body' => 'This section keeps the issues assigned to you in the current project easy to revisit.',
                ],
                'activity' => [
                    'title' => 'Recent activity',
                    'body' => 'Project activity shows recent changes touching the project and its issues so you can catch up quickly.',
                ],
                'sidebar' => [
                    'title' => 'Project details and admin tools',
                    'body' => 'The sidebar is where you review core project metadata, quick-create work, and milestone or admin links.',
                ],
            ],
        ],
        'issue-detail' => [
            'label' => 'Issue walkthrough',
            'steps' => [
                'actions' => [
                    'title' => 'Issue actions',
                    'body' => 'These actions let you move between project views, change status, start timers, and jump into editing.',
                ],
                'header' => [
                    'title' => 'Issue summary',
                    'body' => 'The issue header keeps the core record details together: status, type, priority, ownership, estimates, and tags.',
                ],
                'related_work' => [
                    'title' => 'Related work',
                    'body' => 'Use linked work to connect this issue to other issues or records when the work spans more than one item.',
                ],
                'details_tabs' => [
                    'title' => 'Issue details',
                    'body' => 'Overview, sub-issues, activity, time, and notes all live here so the issue stays the source of truth for execution.',
                ],
                'attachments' => [
                    'title' => 'Attachments',
                    'body' => 'Add screenshots, files, and other supporting material directly on the issue when the work needs context.',
                ],
                'comments' => [
                    'title' => 'Comments',
                    'body' => 'Comments are the main collaboration thread for the issue and are the best place for updates or decisions.',
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

<?php

return [
    'tours' => [
        'main-app' => [
            'label' => 'Main app',
            'steps' => [
                'dashboard' => [
                    'title' => 'Your dashboard',
                    'body' => 'Start here for your active workspace, key stats, and recent activity across Forge.',
                ],
                'workspaces' => [
                    'title' => 'Workspace presets',
                    'body' => 'Switch between workspace presets like my sprint, team delivery, support queue, release health, exec summary, and solo mode without leaving the dashboard route.',
                ],
                'dashboard_layout' => [
                    'title' => 'Layout controls',
                    'body' => 'Each workspace keeps its own widget visibility, order, and landing-page setting, so the dashboard can adapt to the way you work.',
                ],
                'issues_nav' => [
                    'title' => 'Issues',
                    'body' => 'Issues now has its own top-level destination in the main navigation, so it is always one click away.',
                ],
                'issue_explorer' => [
                    'title' => 'Issue Explorer',
                    'body' => 'Use Issue Explorer to query work across projects, layer structured filters, and save reusable views for yourself or your team.',
                ],
                'projects' => [
                    'title' => 'Projects',
                    'body' => 'Projects organize delivery, planning, backlog, roadmap, and issue workflows for each initiative.',
                ],
                'goals' => [
                    'title' => 'Goals',
                    'body' => 'Goals keep longer-term outcomes visible across projects and issues, especially when the work spans teams.',
                ],
                'support' => [
                    'title' => 'Support menu',
                    'body' => 'Open the support portal, submit a ticket, access customer tickets, or jump into triage from this menu depending on your role.',
                ],
                'search' => [
                    'title' => 'Search',
                    'body' => 'Search is the fastest way to jump to projects, issues, organizations, goals, and people once you know what you need.',
                ],
                'create' => [
                    'title' => 'Create from anywhere',
                    'body' => 'Use Create to add new issues, projects, organizations, and goals without leaving the current page.',
                ],
                'teams' => [
                    'title' => 'Teams',
                    'body' => 'Open the team dashboard, switch teams, manage membership, and reach team settings from here.',
                ],
                'account' => [
                    'title' => 'Account and help',
                    'body' => 'Manage your profile, API tokens, and relaunch onboarding or the Getting Started guide later.',
                ],
            ],
        ],
        'project-detail' => [
            'label' => 'Project walkthrough',
            'steps' => [
                'header' => [
                    'title' => 'Project overview',
                    'body' => 'This header gives you the project identity, organization and team context, and the most common project-level actions.',
                ],
                'tabs' => [
                    'title' => 'Project views',
                    'body' => 'Move between overview, backlog, board, scrum, calendar, timeline, roadmap, code, and transition screens from the project tabs.',
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
                    'body' => 'The sidebar is where you review core project metadata, quick-create work, and reach milestone or admin links.',
                ],
                'backlog_controls' => [
                    'title' => 'Backlog planning controls',
                    'body' => 'Use the backlog surface to filter work, focus on a sprint, select issues in bulk, create a sprint, and move planned work into it.',
                ],
                'backlog_queue' => [
                    'title' => 'Prioritized backlog',
                    'body' => 'This queue is where you groom work that is not in a sprint yet. Rank it, size it, and move the right issues into the next sprint.',
                ],
                'backlog_sprints' => [
                    'title' => 'Sprint plan and capacity',
                    'body' => 'Each planned sprint shows committed work, remaining capacity, and a place to review what is already scheduled before you start the sprint.',
                ],
            ],
        ],
        'issue-detail' => [
            'label' => 'Issue walkthrough',
            'steps' => [
                'actions' => [
                    'title' => 'Issue actions',
                    'body' => 'These actions let you jump between backlog, board, sprint, calendar, timeline, and milestones, then change status, start timers, or edit the issue.',
                ],
                'header' => [
                    'title' => 'Issue summary',
                    'body' => 'The issue header keeps the core record details together: status, type, priority, ownership, milestones, estimates, and progress.',
                ],
                'related_work' => [
                    'title' => 'Related work',
                    'body' => 'Use linked work to connect this issue to other issues or records when the work spans more than one item.',
                ],
                'codex_pages' => [
                    'title' => 'Codex Pages',
                    'body' => 'Link supporting docs and reference pages from Codex so delivery context stays attached to the issue.',
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
                'followers' => [
                    'title' => 'Followers',
                    'body' => 'Followers keep the right people subscribed to issue activity and let collaborators opt in without becoming the assignee.',
                ],
                'code_links' => [
                    'title' => 'Code links',
                    'body' => 'When the project is linked to a repository, branches and pull requests connected to the issue keep implementation traceable.',
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

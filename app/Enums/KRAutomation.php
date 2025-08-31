<?php

namespace App\Enums;

enum KRAutomation: string
{
    case Manual = 'manual';
    case IssuesDonePercent = 'issues_done_percent';
    case StoryPointsDonePercent = 'story_points_done_percent';
}

<?php

namespace App\Enums;

enum IssueTier: string
{
    case Epic = 'epic';
    case Story = 'story';
    case Task = 'task';
    case SubTask = 'subtask';
    case Other = 'other';
}

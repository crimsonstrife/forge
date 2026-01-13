<?php

namespace App\Enums;

enum MilestoneState: string
{
    case Planned = 'planned';
    case Active = 'active';
    case Completed = 'completed';
    case Canceled = 'canceled';
}

<?php
namespace App\Enums;

enum GoalType: string
{
    case Objective = 'objective';
    case KPI = 'kpi';
    case SMART = 'smart';
    case Initiative = 'initiative';
}

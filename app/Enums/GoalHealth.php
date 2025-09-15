<?php
namespace App\Enums;

enum GoalHealth: string
{
    case OnTrack = 'on_track';
    case AtRisk  = 'at_risk';
    case OffTrack = 'off_track';
}

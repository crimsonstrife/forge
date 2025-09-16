<?php
namespace App\Enums;

enum KRDirection: string
{
    case IncreaseTo='increase_to';
    case DecreaseTo='decrease_to';
    case MaintainBetween='maintain_between';
    case HitExact='hit_exact';
}

<?php
namespace App\Enums;

enum GoalStatus: string
{
    case Draft='draft';
    case Active='active';
    case Paused='paused';
    case Completed='completed';
    case Canceled='canceled';
}

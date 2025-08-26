<?php

namespace App\Enums;

enum ProjectStage: string
{
    case Planning   = 'planning';
    case Active     = 'active';
    case Maintenance= 'maintenance';
    case Frozen     = 'frozen';
    case Archived   = 'archived';
}

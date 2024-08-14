<?php

namespace App\Filament\Resources\ProjectStatusResource\Pages;

use App\Filament\Resources\ProjectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateProjectStatus
 *
 * This class represents a create project status page.
 */
class CreateProjectStatus extends CreateRecord
{
    protected static string $resource = ProjectStatusResource::class;
}

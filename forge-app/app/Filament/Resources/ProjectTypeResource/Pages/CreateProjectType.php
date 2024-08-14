<?php

namespace App\Filament\Resources\ProjectTypeResource\Pages;

use App\Filament\Resources\ProjectTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateProjectType
 *
 * This class represents a create project type page.
 */
class CreateProjectType extends CreateRecord
{
    protected static string $resource = ProjectTypeResource::class;
}

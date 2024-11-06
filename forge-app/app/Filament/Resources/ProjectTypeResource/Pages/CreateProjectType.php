<?php

namespace App\Filament\Resources\ProjectTypeResource\Pages;

use App\Filament\Resources\ProjectTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateProjectType
 *
 * This class handles the creation of a new project type record.
 * It extends the CreateRecord class provided by the Filament package.
 *
 * @package App\Filament\Resources\ProjectTypeResource\Pages
 */
class CreateProjectType extends CreateRecord
{
    protected static string $resource = ProjectTypeResource::class;
}

<?php

namespace App\Filament\Resources\ProjectStatusResource\Pages;

use App\Filament\Resources\ProjectStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateProjectStatus
 *
 * This class handles the creation of a new project status record.
 * It extends the CreateRecord class provided by the Filament framework.
 *
 * @package App\Filament\Resources\ProjectStatusResource\Pages
 */
class CreateProjectStatus extends CreateRecord
{
    protected static string $resource = ProjectStatusResource::class;
}

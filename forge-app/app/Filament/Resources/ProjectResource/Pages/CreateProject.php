<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateProject
 *
 * This class represents a create project page.
 */
class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;
}

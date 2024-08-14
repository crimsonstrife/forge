<?php

namespace App\Filament\Resources\IssueTypeResource\Pages;

use App\Filament\Resources\IssueTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIssueType
 *
 * This class represents a create issue type page.
 */
class CreateIssueType extends CreateRecord
{
    protected static string $resource = IssueTypeResource::class;
}

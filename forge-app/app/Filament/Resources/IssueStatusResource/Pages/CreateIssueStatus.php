<?php

namespace App\Filament\Resources\IssueStatusResource\Pages;

use App\Filament\Resources\IssueStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

/**
 * Class CreateIssueStatus
 *
 * This class represents a create issue status page.
 */
class CreateIssueStatus extends CreateRecord
{
    protected static string $resource = IssueStatusResource::class;
}
